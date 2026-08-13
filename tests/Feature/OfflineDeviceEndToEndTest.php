<?php

use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Http\Client\Request as ClientRequest;
use Illuminate\Support\Facades\Http;
use Laravel\Sanctum\Sanctum;
use Modules\Curriculum\Models\Curriculum;
use Modules\FeeManagement\Models\Fee;
use Modules\Institution\Models\Institution;
use Modules\Parent\Models\ParentDetails;
use Modules\Student\Models\StudentDetails;

/**
 * The test I should have written before claiming any of this worked: a
 * device pulling real records over its real sync client, then opening the
 * pages a bursar actually uses - with client mode on, so every gate that
 * would bounce a device is live.
 *
 * Tether's client makes genuine HTTP calls, so those are routed back into
 * this application's own routes. Client and server share one database
 * here, which is the honest limit of this test: it proves the endpoints,
 * scoping, guards and screens work, not that two separate databases
 * converge.
 */
beforeEach(function () {
    $this->seed(PermissionSeeder::class);
});

/**
 * Point the sync client's outbound HTTP at the test application, carrying
 * the device's token.
 */
function routeSyncTrafficToTheApp($test, string $token): void
{
    config([
        'tether-client.server_routes.push' => 'http://localhost/tether/push',
        'tether-client.server_routes.pull' => 'http://localhost/tether/pull',
    ]);

    Http::fake(function (ClientRequest $request) use ($test, $token) {
        $response = $test->withToken($token)
            ->postJson(parse_url($request->url(), PHP_URL_PATH), $request->data());

        return Http::response($response->json(), $response->status());
    });
}

function seedSchoolOnServer(): array
{
    $director = User::factory()->create(['name' => 'Grace Director']);
    $director->assignRole('Director');

    $curriculum = Curriculum::create(['name' => 'CBC', 'status' => 'active']);

    $institution = Institution::create([
        'user_id' => $director->id,
        'name' => 'Riverside Academy',
        'code' => 'INST-'.uniqid(),
        'type' => 'School',
        'curriculum' => $curriculum->id,
        'is_active' => true,
        'status' => 'active',
        'is_approved' => true,
    ]);

    $director->update(['active_institution_id' => $institution->id]);

    $studentUser = User::factory()->create(['name' => 'Amina Otieno']);
    $studentUser->assignRole('Student');

    $parentUser = User::factory()->create(['name' => 'Joseph Otieno']);
    $parentUser->assignRole('Parent');
    $parent = ParentDetails::create([
        'parent_id' => $parentUser->id,
        'parent_phone' => '07'.random_int(10000000, 99999999),
    ]);

    StudentDetails::create([
        'student_id' => $studentUser->id,
        'parent_id' => $parent->id,
        'institution_id' => $institution->id,
        'admission_number' => 'ADM-'.uniqid(),
        'enrollment_status' => 'active',
    ]);

    Fee::create([
        'institution_id' => $institution->id,
        'student_id' => $studentUser->id,
        'title' => 'Term 1 Tuition',
        'fee_type' => 'tuition',
        'amount' => 10000,
        'amount_paid' => 0,
    ]);

    return [$director->refresh(), $institution, $studentUser];
}

test('a paired device pulls its school and opens the fee screen', function () {
    [$director] = seedSchoolOnServer();

    $token = $director->createToken('device', ['sync'])->plainTextToken;
    routeSyncTrafficToTheApp($this, $token);

    config()->set('sync.client_mode', true);

    $this->artisan('tether:pull')->assertSuccessful();

    // The gate that used to bounce a device into onboarding.
    $response = $this->actingAs($director)->get('/dashboard');
    $response->assertOk();

    // And the screen that used to render blank names, because students
    // weren't synced.
    $this->actingAs($director)->get(route('feemanagement.index'))
        ->assertOk()
        ->assertSee('Term 1 Tuition')
        ->assertSee('Amina Otieno');
});

test('a device cannot push reference data back', function () {
    [$director, $institution] = seedSchoolOnServer();

    $token = $director->createToken('device', ['sync'])->plainTextToken;

    $response = $this->withToken($token)->postJson(route('tether.push'), [
        'client_id' => 'device-1',
        'mutations' => [[
            'mutation_id' => (string) Str::uuid(),
            'entity_id' => $institution->tether_id,
            'model' => 'Institution',
            'operation' => 'update',
            'version' => 1,
            'timestamp' => now()->getPreciseTimestamp(3),
            'payload' => ['name' => 'Renamed By A Device'],
        ]],
    ]);

    // The push endpoint answers 200 and reports per-mutation outcomes -
    // one bad mutation doesn't fail a device's whole batch - so the refusal
    // shows up as a rejection, not an HTTP error.
    $response->assertOk();

    expect($response->json('applied'))->toBe([])
        ->and($response->json('rejected'))->not->toBe([])
        ->and($institution->fresh()->name)->toBe('Riverside Academy');
});

test('a pulled user carries no usable credential', function () {
    [$director] = seedSchoolOnServer();

    Sanctum::actingAs($director, ['sync']);

    $response = $this->postJson(route('tether.pull'), ['client_id' => 'device-1']);

    $response->assertOk();

    $users = collect($response->json('snapshots'))
        ->filter(fn (array $snapshot) => str_contains((string) ($snapshot['model'] ?? ''), 'User'));

    expect($users)->not->toBeEmpty();

    $users->each(function (array $snapshot) use ($director) {
        expect($snapshot['payload']['password'] ?? null)->toBe('no-local-login')
            ->and($snapshot['payload']['password'] ?? null)->not->toBe($director->password);
    });
});

test('one school never pulls another', function () {
    [$director] = seedSchoolOnServer();
    [$otherDirector] = seedSchoolOnServer();

    Sanctum::actingAs($otherDirector, ['sync']);

    $response = $this->postJson(route('tether.pull'), ['client_id' => 'device-2']);

    $payloads = json_encode($response->json());

    // Two schools exist, each with one identically titled fee and one
    // student. A device that pulled both would see the title twice.
    expect(Fee::count())->toBe(2)
        ->and(substr_count($payloads, 'Term 1 Tuition'))->toBe(1)
        ->and($payloads)->not->toContain($director->activeInstitution->code);
});
