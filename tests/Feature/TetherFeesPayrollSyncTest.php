<?php

use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;
use Modules\FeeManagement\Models\Fee;
use Modules\FeeManagement\Models\FeePayment;
use Modules\Institution\Models\Institution;
use Modules\Staff\Models\StaffDetails;
use Modules\Staff\Models\StaffPayment;

beforeEach(function () {
    $this->seed(PermissionSeeder::class);
});

function makeSyncSchool(): Institution
{
    $owner = User::factory()->create();
    $owner->assignRole('Director');

    $institution = Institution::create([
        'user_id' => $owner->id,
        'name' => 'School '.uniqid(),
        'code' => 'INST-'.uniqid(),
        'type' => 'School',
        'is_active' => true,
        'status' => 'active',
        'is_approved' => true,
    ]);

    $owner->update(['active_institution_id' => $institution->id]);

    return $institution;
}

function syncDeviceFor(Institution $institution): User
{
    return User::where('active_institution_id', $institution->id)->firstOrFail();
}

function makeSyncFee(Institution $institution, array $overrides = []): Fee
{
    $student = User::factory()->create();
    $student->assignRole('Student');

    return Fee::create(array_merge([
        'institution_id' => $institution->id,
        'student_id' => $student->id,
        'title' => 'Term 1 Tuition',
        'fee_type' => 'tuition',
        'amount' => 10000,
        'amount_paid' => 0,
    ], $overrides));
}

test('synced models get a tether id automatically', function () {
    $institution = makeSyncSchool();

    $fee = makeSyncFee($institution);
    $staff = StaffDetails::create([
        'institution_id' => $institution->id,
        'name' => 'Jane Bursar',
        'status' => 'active',
    ]);

    expect($fee->tether_id)->toBeString()->toHaveLength(26)
        ->and($staff->tether_id)->toBeString()->toHaveLength(26);
});

test('a device only pulls its own school records', function () {
    $ours = makeSyncSchool();
    $theirs = makeSyncSchool();

    makeSyncFee($ours, ['title' => 'Our Tuition']);
    makeSyncFee($theirs, ['title' => 'Their Tuition']);

    Sanctum::actingAs(syncDeviceFor($ours), ['sync']);

    $response = $this->postJson(route('tether.pull'), ['client_id' => 'device-1']);

    $response->assertOk();

    $body = $response->json();
    $payloads = json_encode($body);

    expect($payloads)->toContain('Our Tuition')
        ->and($payloads)->not->toContain('Their Tuition');
});

test('a pushed record lands in the device own school even if it claims another', function () {
    $ours = makeSyncSchool();
    $theirs = makeSyncSchool();

    $staff = StaffDetails::create([
        'institution_id' => $ours->id,
        'name' => 'Jane Bursar',
        'status' => 'active',
    ]);

    $entityId = (string) Str::ulid();

    Sanctum::actingAs(syncDeviceFor($ours), ['sync']);

    $response = $this->postJson(route('tether.push'), [
        'client_id' => 'device-1',
        'mutations' => [[
            'mutation_id' => (string) Str::uuid(),
            'entity_id' => $entityId,
            'model' => 'StaffPayment',
            'operation' => 'create',
            'version' => 1,
            'timestamp' => now()->getPreciseTimestamp(3),
            'payload' => [
                // A compromised or buggy client claiming another school.
                'institution_id' => $theirs->id,
                'staff_details_id' => $staff->id,
                'period' => '2026-08-01',
                'gross_amount' => 50000,
                'net_amount' => 50000,
                'status' => 'paid',
            ],
        ]],
    ]);

    $response->assertOk();
    // Surfaces the rejection reason if the mutation didn't apply, instead
    // of just a null model further down.
    expect($response->json('rejected'))->toBe([]);

    $payment = StaffPayment::where('tether_id', $entityId)->first();

    expect($payment)->not->toBeNull()
        ->and($payment->institution_id)->toBe($ours->id)
        ->and($payment->institution_id)->not->toBe($theirs->id);
});

test('a synced fee payment updates the fee balance', function () {
    $institution = makeSyncSchool();
    $fee = makeSyncFee($institution, ['amount' => 10000, 'amount_paid' => 0]);

    $entityId = (string) Str::ulid();

    Sanctum::actingAs(syncDeviceFor($institution), ['sync']);

    $response = $this->postJson(route('tether.push'), [
        'client_id' => 'device-1',
        'mutations' => [[
            'mutation_id' => (string) Str::uuid(),
            'entity_id' => $entityId,
            'model' => 'FeePayment',
            'operation' => 'create',
            'version' => 1,
            'timestamp' => now()->getPreciseTimestamp(3),
            'payload' => [
                'fee_id' => $fee->id,
                'student_id' => $fee->student_id,
                'amount' => 2500,
                'payment_method' => 'cash',
                'paid_at' => today()->toDateString(),
                'source' => 'offline_sync',
            ],
        ]],
    ]);

    $response->assertOk();
    expect($response->json('rejected'))->toBe([]);
    expect(FeePayment::where('tether_id', $entityId)->exists())->toBeTrue();

    // The applicator writes the payment row directly, bypassing
    // FeePaymentService - the reconciliation listener is what keeps the
    // aggregate everything else reads correct.
    expect((float) $fee->fresh()->amount_paid)->toBe(2500.0);
});

test('reconciliation is idempotent across repeated pushes', function () {
    $institution = makeSyncSchool();
    $fee = makeSyncFee($institution, ['amount' => 10000, 'amount_paid' => 0]);

    $mutation = [
        'mutation_id' => (string) Str::uuid(),
        'entity_id' => (string) Str::ulid(),
        'model' => 'FeePayment',
        'operation' => 'create',
        'version' => 1,
        'timestamp' => now()->getPreciseTimestamp(3),
        'payload' => [
            'fee_id' => $fee->id,
            'student_id' => $fee->student_id,
            'amount' => 2500,
            'payment_method' => 'cash',
            'paid_at' => today()->toDateString(),
        ],
    ];

    // A flaky connection means clients retry the same mutation.
    foreach (range(1, 3) as $attempt) {
        Sanctum::actingAs(syncDeviceFor($institution), ['sync']);

        $this->postJson(route('tether.push'), [
            'client_id' => 'device-1',
            'mutations' => [$mutation],
        ])->assertOk();
    }

    expect(FeePayment::where('fee_id', $fee->id)->count())->toBe(1)
        ->and((float) $fee->fresh()->amount_paid)->toBe(2500.0);
});
