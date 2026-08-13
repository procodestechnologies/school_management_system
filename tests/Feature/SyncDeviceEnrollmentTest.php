<?php

use App\Models\SyncDevice;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Laravel\Sanctum\PersonalAccessToken;
use Modules\Institution\Models\Institution;
use Modules\Staff\Models\StaffDetails;

beforeEach(function () {
    $this->seed(PermissionSeeder::class);
});

function makeEnrollingDirector(): array
{
    $director = User::factory()->create();
    $director->assignRole('Director');

    $institution = Institution::create([
        'user_id' => $director->id,
        'name' => 'School '.uniqid(),
        'code' => 'INST-'.uniqid(),
        'type' => 'School',
        'is_active' => true,
        'status' => 'active',
        'is_approved' => true,
    ]);

    $director->update(['active_institution_id' => $institution->id]);

    return [$director->refresh(), $institution];
}

test('a director enrolls a device and is shown the token once', function () {
    [$director, $institution] = makeEnrollingDirector();

    $response = $this->actingAs($director)->post(route('sync-devices.store'), [
        'name' => "Bursar's laptop",
        'platform' => 'desktop',
        'user_id' => $director->id,
    ]);

    $response->assertRedirect(route('sync-devices.index'));
    $response->assertSessionHas('device_token');

    $device = SyncDevice::firstOrFail();

    expect($device->institution_id)->toBe($institution->id)
        ->and($device->client_id)->toHaveLength(26)
        ->and($device->isActive())->toBeTrue();

    // Only a hash is stored - the plaintext exists solely in that flash.
    $token = PersonalAccessToken::findOrFail($device->token_id);
    expect($token->abilities)->toBe(['sync']);
});

test('an enrolled device token can sync and nothing else', function () {
    [$director] = makeEnrollingDirector();

    $plainTextToken = $director->createToken('device', ['sync'])->plainTextToken;

    $this->withHeader('Authorization', 'Bearer '.$plainTextToken)
        ->postJson(route('tether.pull'), ['client_id' => 'device-1'])
        ->assertOk();
});

test('a token without the sync ability is refused', function () {
    [$director] = makeEnrollingDirector();

    // A token minted for something else must not double as a sync key.
    $plainTextToken = $director->createToken('other', ['reports'])->plainTextToken;

    $this->withHeader('Authorization', 'Bearer '.$plainTextToken)
        ->postJson(route('tether.pull'), ['client_id' => 'device-1'])
        ->assertForbidden();
});

test('revoking a device stops it syncing but keeps the record', function () {
    [$director] = makeEnrollingDirector();

    $this->actingAs($director)->post(route('sync-devices.store'), [
        'name' => 'Lost laptop',
        'platform' => 'desktop',
        'user_id' => $director->id,
    ]);

    $device = SyncDevice::firstOrFail();
    $plainTextToken = session('device_token');

    $this->actingAs($director)->delete(route('sync-devices.destroy', $device))
        ->assertRedirect(route('sync-devices.index'));

    $device->refresh();

    expect($device->exists)->toBeTrue()
        ->and($device->isActive())->toBeFalse()
        ->and($device->revoked_at)->not->toBeNull()
        ->and(PersonalAccessToken::count())->toBe(0);

    // The Director's browser session would otherwise still authenticate
    // the next request - auth:sanctum falls back to the session guard -
    // and we're testing the token, not the session.
    $this->flushSession();
    $this->app['auth']->forgetGuards();

    $this->withHeader('Authorization', 'Bearer '.$plainTextToken)
        ->postJson(route('tether.pull'), ['client_id' => 'device-1'])
        ->assertUnauthorized();
});

test('a device cannot be enrolled against another school account', function () {
    [$director] = makeEnrollingDirector();
    [$otherDirector] = makeEnrollingDirector();

    $this->actingAs($director)->post(route('sync-devices.store'), [
        'name' => 'Sneaky laptop',
        'platform' => 'desktop',
        'user_id' => $otherDirector->id,
    ])->assertForbidden();

    expect(SyncDevice::count())->toBe(0);
});

test('an accountant of the school is an eligible sync account', function () {
    [$director, $institution] = makeEnrollingDirector();

    $accountant = User::factory()->create();
    $accountant->assignRole('Accountant');
    StaffDetails::create([
        'institution_id' => $institution->id,
        'user_id' => $accountant->id,
        'name' => $accountant->name,
        'status' => 'active',
    ]);

    $this->actingAs($director)->post(route('sync-devices.store'), [
        'name' => 'Accountant tablet',
        'platform' => 'android',
        'user_id' => $accountant->id,
    ])->assertRedirect(route('sync-devices.index'));

    expect(SyncDevice::firstOrFail()->user_id)->toBe($accountant->id);
});

test('an accountant cannot enroll devices', function () {
    [, $institution] = makeEnrollingDirector();

    $accountant = User::factory()->create();
    $accountant->assignRole('Accountant');
    StaffDetails::create([
        'institution_id' => $institution->id,
        'user_id' => $accountant->id,
        'name' => $accountant->name,
        'status' => 'active',
    ]);

    $this->actingAs($accountant)->get(route('sync-devices.create'))->assertForbidden();
    $this->actingAs($accountant)->get(route('sync-devices.index'))->assertForbidden();
});

test('the device list renders', function () {
    [$director] = makeEnrollingDirector();

    $this->actingAs($director)->post(route('sync-devices.store'), [
        'name' => "Bursar's laptop",
        'platform' => 'desktop',
        'user_id' => $director->id,
    ]);

    $this->actingAs($director)->get(route('sync-devices.index'))
        ->assertOk()
        ->assertSee("Bursar's laptop")
        ->assertSee('Active');

    $this->actingAs($director)->get(route('sync-devices.create'))->assertOk();
});
