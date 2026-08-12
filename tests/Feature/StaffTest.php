<?php

use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Modules\Institution\Models\Institution;
use Modules\Staff\Models\StaffDetails;
use Modules\Staff\Models\StaffPayment;

// Every staff/payroll screen gates on permissions, so the real roles have
// to exist.
beforeEach(function () {
    $this->seed(PermissionSeeder::class);
});

function makeStaffInstitution(User $owner): Institution
{
    return Institution::create([
        'user_id' => $owner->id,
        'name' => 'Test Institution '.uniqid(),
        'code' => 'INST-'.uniqid(),
        'type' => 'School',
        'is_active' => true,
        'status' => 'active',
        'is_approved' => true,
    ]);
}

function makeDirectorWithInstitution(): array
{
    $director = User::factory()->create();
    $director->assignRole('Director');

    $institution = makeStaffInstitution($director);
    $director->update(['active_institution_id' => $institution->id]);

    return [$director->refresh(), $institution];
}

function makeStaffMember(Institution $institution, array $overrides = []): StaffDetails
{
    return StaffDetails::create(array_merge([
        'institution_id' => $institution->id,
        'name' => 'Jane Bursar',
        'job_title' => 'Bursar',
        'salary' => 50000,
        'status' => 'active',
        'is_active' => true,
    ], $overrides));
}

/**
 * An Accountant is a staff member with a login - the same shape the Staff
 * module creates.
 */
function makeAccountant(Institution $institution): User
{
    $accountant = User::factory()->create();
    $accountant->assignRole('Accountant');

    makeStaffMember($institution, [
        'user_id' => $accountant->id,
        'name' => $accountant->name,
        'email' => $accountant->email,
        'job_title' => 'Accountant',
    ]);

    return $accountant->refresh();
}

test('a director can create a staff member without a login', function () {
    [$director, $institution] = makeDirectorWithInstitution();

    $response = $this->actingAs($director)->post(route('staff.store'), [
        'name' => 'Peter Cook',
        'phone' => '0700000000',
        'job_title' => 'Cook',
        'employment_type' => 'full_time',
        'salary' => 25000,
        'status' => 'active',
        'is_active' => 1,
    ]);

    $response->assertRedirect(route('staff.index'));

    $staff = StaffDetails::where('name', 'Peter Cook')->first();
    expect($staff)->not->toBeNull()
        ->and($staff->institution_id)->toBe($institution->id)
        ->and($staff->user_id)->toBeNull();
});

test('a director can give a staff member an accountant login', function () {
    [$director] = makeDirectorWithInstitution();

    $this->actingAs($director)->post(route('staff.store'), [
        'name' => 'Grace Accounts',
        'email' => 'grace@example.com',
        'job_title' => 'Accountant',
        'status' => 'active',
        'create_account' => 1,
        'system_role' => 'Accountant',
        'password' => 'password123',
    ])->assertRedirect(route('staff.index'));

    $staff = StaffDetails::where('name', 'Grace Accounts')->firstOrFail();
    $account = User::find($staff->user_id);

    expect($account)->not->toBeNull()
        ->and($account->email)->toBe('grace@example.com')
        ->and($account->hasRole('Accountant'))->toBeTrue();
});

test('creating a login without a password is rejected', function () {
    [$director] = makeDirectorWithInstitution();

    $this->actingAs($director)
        ->post(route('staff.store'), [
            'name' => 'No Password',
            'email' => 'nopassword@example.com',
            'create_account' => 1,
            'system_role' => 'Accountant',
        ])
        ->assertSessionHasErrors('password');

    expect(StaffDetails::where('name', 'No Password')->exists())->toBeFalse();
});

test('an accountant resolves the institution of their staff record', function () {
    [, $institution] = makeDirectorWithInstitution();
    $accountant = makeAccountant($institution);

    $this->actingAs($accountant);

    expect(currentInstitution()?->id)->toBe($institution->id);
});

test('an accountant can view staff but cannot create, edit or delete them', function () {
    [, $institution] = makeDirectorWithInstitution();
    $accountant = makeAccountant($institution);
    $staff = makeStaffMember($institution, ['name' => 'Peter Cook']);

    $this->actingAs($accountant)->get(route('staff.index'))->assertOk()->assertSee('Peter Cook');
    $this->actingAs($accountant)->get(route('staff.create'))->assertForbidden();
    $this->actingAs($accountant)->get(route('staff.edit', $staff))->assertForbidden();
    $this->actingAs($accountant)->delete(route('staff.destroy', $staff))->assertForbidden();
});

test('an accountant can record a staff payment and the net is calculated', function () {
    [, $institution] = makeDirectorWithInstitution();
    $accountant = makeAccountant($institution);
    $staff = makeStaffMember($institution, ['name' => 'Peter Cook']);

    $this->actingAs($accountant)->post(route('staff.payments.store'), [
        'staff_details_id' => $staff->id,
        'period' => '2026-08',
        'gross_amount' => 25000,
        'allowances' => 2500,
        'deductions' => 500,
        'payment_method' => 'bank_transfer',
        'status' => 'paid',
    ])->assertRedirect(route('staff.payments.index'));

    $payment = StaffPayment::where('staff_details_id', $staff->id)->firstOrFail();

    expect((float) $payment->net_amount)->toBe(27000.0)
        ->and($payment->institution_id)->toBe($institution->id)
        ->and($payment->recorded_by)->toBe($accountant->id)
        ->and($payment->paid_at)->not->toBeNull()
        ->and($payment->period->format('Y-m-d'))->toBe('2026-08-01');
});

test('the same staff member cannot be paid twice for one month', function () {
    [, $institution] = makeDirectorWithInstitution();
    $accountant = makeAccountant($institution);
    $staff = makeStaffMember($institution, ['name' => 'Peter Cook']);

    $payload = [
        'staff_details_id' => $staff->id,
        'period' => '2026-08',
        'gross_amount' => 25000,
        'payment_method' => 'cash',
        'status' => 'pending',
    ];

    $this->actingAs($accountant)->post(route('staff.payments.store'), $payload);
    $this->actingAs($accountant)->post(route('staff.payments.store'), $payload)->assertSessionHas('error');

    expect(StaffPayment::where('staff_details_id', $staff->id)->count())->toBe(1);
});

test('an accountant cannot pay another school\'s staff', function () {
    [, $institution] = makeDirectorWithInstitution();
    [, $otherInstitution] = makeDirectorWithInstitution();

    $accountant = makeAccountant($institution);
    $outsider = makeStaffMember($otherInstitution, ['name' => 'Someone Else']);

    $this->actingAs($accountant)->post(route('staff.payments.store'), [
        'staff_details_id' => $outsider->id,
        'period' => '2026-08',
        'gross_amount' => 1000,
        'payment_method' => 'cash',
        'status' => 'pending',
    ])->assertForbidden();

    expect(StaffPayment::count())->toBe(0);
});

test('an accountant cannot reach modules outside payroll and fees', function () {
    [, $institution] = makeDirectorWithInstitution();
    $accountant = makeAccountant($institution);

    $this->actingAs($accountant)->get(route('staff.payments.index'))->assertOk();
    $this->actingAs($accountant)->get(route('feemanagement.index'))->assertOk();
    $this->actingAs($accountant)->get(route('curriculum.index'))->assertForbidden();
    $this->actingAs($accountant)->get(route('timetable.index'))->assertForbidden();
});

test('a director only sees their own school\'s staff', function () {
    [$director, $institution] = makeDirectorWithInstitution();
    [, $otherInstitution] = makeDirectorWithInstitution();

    makeStaffMember($institution, ['name' => 'Our Bursar']);
    makeStaffMember($otherInstitution, ['name' => 'Their Bursar']);

    $this->actingAs($director)->get(route('staff.index'))
        ->assertOk()
        ->assertSee('Our Bursar')
        ->assertDontSee('Their Bursar');
});

test('every staff and payroll screen renders', function () {
    [$director, $institution] = makeDirectorWithInstitution();
    $staff = makeStaffMember($institution, ['name' => 'Peter Cook']);

    $payment = StaffPayment::create([
        'staff_details_id' => $staff->id,
        'institution_id' => $institution->id,
        'recorded_by' => $director->id,
        'period' => '2026-08-01',
        'gross_amount' => 25000,
        'allowances' => 0,
        'deductions' => 0,
        'net_amount' => 25000,
        'payment_method' => 'cash',
        'status' => 'pending',
    ]);

    $this->actingAs($director);

    $this->get(route('staff.index'))->assertOk();
    $this->get(route('staff.create'))->assertOk()->assertSee('System Access');
    $this->get(route('staff.show', $staff))->assertOk()->assertSee('Peter Cook');
    $this->get(route('staff.edit', $staff))->assertOk();
    $this->get(route('staff.payments.index'))->assertOk()->assertSee('Total Payroll');
    $this->get(route('staff.payments.create'))->assertOk();
    $this->get(route('staff.payments.show', $payment))->assertOk()->assertSee('Net Pay');
    $this->get(route('staff.payments.edit', $payment))->assertOk();
});

test('deleting a staff member also removes their login', function () {
    [$director, $institution] = makeDirectorWithInstitution();
    $accountant = makeAccountant($institution);
    $staff = StaffDetails::where('user_id', $accountant->id)->firstOrFail();

    $this->actingAs($director)->delete(route('staff.destroy', $staff))
        ->assertRedirect(route('staff.index'));

    expect(StaffDetails::find($staff->id))->toBeNull()
        ->and(User::find($accountant->id))->toBeNull();
});
