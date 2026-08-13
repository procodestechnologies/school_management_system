<?php

use App\Models\SyncSetting;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Laravel\Sanctum\Sanctum;
use Modules\Institution\Models\Institution;
use Modules\Staff\Models\StaffDetails;

beforeEach(function () {
    $this->seed(PermissionSeeder::class);
});

function makePairingDirector(): array
{
    $director = User::factory()->create(['name' => 'Grace Director']);
    $director->assignRole('Director');

    $institution = Institution::create([
        'user_id' => $director->id,
        'name' => 'Riverside Academy',
        'code' => 'INST-'.uniqid(),
        'type' => 'School',
        'is_active' => true,
        'status' => 'active',
        'is_approved' => true,
    ]);

    $director->update(['active_institution_id' => $institution->id]);

    return [$director->refresh(), $institution];
}

test('the profile endpoint tells a device who it syncs as', function () {
    [$director, $institution] = makePairingDirector();

    Sanctum::actingAs($director, ['sync']);

    $response = $this->getJson(route('tether.device.profile'));

    $response->assertOk()
        ->assertJsonPath('user.id', $director->id)
        ->assertJsonPath('user.email', $director->email)
        ->assertJsonPath('user.roles.0', 'Director')
        ->assertJsonPath('institution.name', 'Riverside Academy');

    // A device never receives anything it could crack back into the
    // account's real credential.
    expect($response->json('user'))->not->toHaveKey('password');
    expect(json_encode($response->json()))->not->toContain($director->password);
});

test('the profile endpoint refuses callers without a sync token', function () {
    [$director] = makePairingDirector();

    $this->getJson(route('tether.device.profile'))->assertUnauthorized();

    Sanctum::actingAs($director, ['reports']);
    $this->getJson(route('tether.device.profile'))->assertForbidden();
});

test('an accountant profile resolves through their staff record', function () {
    [, $institution] = makePairingDirector();

    $accountant = User::factory()->create();
    $accountant->assignRole('Accountant');
    StaffDetails::create([
        'institution_id' => $institution->id,
        'user_id' => $accountant->id,
        'name' => $accountant->name,
        'status' => 'active',
    ]);

    Sanctum::actingAs($accountant->refresh(), ['sync']);

    $this->getJson(route('tether.device.profile'))
        ->assertOk()
        ->assertJsonPath('user.roles.0', 'Accountant')
        ->assertJsonPath('institution.id', $institution->id);
});

test('pairing builds a local account that signs in offline', function () {
    config()->set('sync.client_mode', true);

    Http::fake([
        '*/tether/device/profile' => Http::response([
            'user' => [
                'id' => 501,
                'name' => 'Grace Director',
                'email' => 'grace@example.com',
                'roles' => ['Director'],
            ],
            // A real server always returns a school - the endpoint 403s
            // otherwise - and this is a foreign key on the device.
            'institution' => ['id' => 7, 'name' => 'Riverside Academy', 'code' => 'RA-1'],
            'server_time' => now()->toIso8601String(),
        ]),
        '*/tether/pull' => Http::response([
            'snapshots' => [],
            'new_sync_cursor' => null,
            'has_more' => false,
        ]),
        '*/tether/push' => Http::response(['applied' => [], 'rejected' => [], 'conflicts' => []]),
    ]);

    $this->artisan('sync:pair', [
        '--server' => 'https://solforbs.com',
        '--token' => 'device-token-value',
        '--passcode' => 'offline-passcode',
    ])->assertSuccessful();

    $local = User::findOrFail(501);

    expect($local->email)->toBe('grace@example.com')
        ->and($local->hasRole('Director'))->toBeTrue()
        // The school didn't arrive in this faked sync, so the account is
        // left unattached rather than pointed at a missing foreign key.
        ->and($local->active_institution_id)->toBeNull()
        // The passcode chosen on the device is what unlocks it - the
        // server's own password hash never travelled.
        ->and(Hash::check('offline-passcode', $local->password))->toBeTrue()
        ->and(SyncSetting::get('device_token'))->toBe('device-token-value')
        ->and(SyncSetting::get('server_url'))->toBe('https://solforbs.com');
});

test('pairing stops when the server rejects the token', function () {
    config()->set('sync.client_mode', true);

    Http::fake([
        '*/tether/device/profile' => Http::response(['message' => 'Unauthenticated.'], 401),
    ]);

    $this->artisan('sync:pair', [
        '--server' => 'https://solforbs.com',
        '--token' => 'revoked-token',
        '--passcode' => 'offline-passcode',
    ])->assertFailed();

    expect(SyncSetting::get('device_token'))->toBeNull()
        ->and(User::find(501))->toBeNull();
});

test('pairing refuses to run on the server build', function () {
    expect(config('sync.client_mode'))->toBeFalse();

    $this->artisan('sync:pair', [
        '--server' => 'https://solforbs.com',
        '--token' => 'whatever',
        '--passcode' => 'offline-passcode',
    ])->assertFailed();
});

test('a short passcode is rejected', function () {
    config()->set('sync.client_mode', true);

    Http::fake([
        '*/tether/device/profile' => Http::response([
            'user' => ['id' => 501, 'name' => 'G', 'email' => 'g@example.com', 'roles' => ['Director']],
            'institution' => ['id' => null, 'name' => 'X', 'code' => 'X'],
        ]),
    ]);

    $this->artisan('sync:pair', [
        '--server' => 'https://solforbs.com',
        '--token' => 'device-token-value',
        '--passcode' => 'short',
    ])->assertFailed();

    expect(User::find(501))->toBeNull();
});
