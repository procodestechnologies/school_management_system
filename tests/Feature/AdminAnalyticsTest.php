<?php

use App\Models\User;
use Modules\Institution\Models\Institution;
use Spatie\Permission\Models\Role;

function makeAnalyticsAdmin(): User
{
    Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);

    $user = User::factory()->create();
    $user->assignRole('Admin');

    return $user;
}

function makeInstitution(array $overrides = []): Institution
{
    $owner = User::factory()->create();

    return Institution::create(array_merge([
        'user_id' => $owner->id,
        'name' => 'Test Institution '.uniqid(),
        'code' => 'INST-'.uniqid(),
        'type' => 'School',
        'is_active' => true,
        'status' => 'active',
        'is_approved' => true,
    ], $overrides));
}

test('the admin sees a system-wide analytics dashboard', function () {
    $admin = makeAnalyticsAdmin();
    $this->actingAs($admin);

    makeInstitution(['name' => 'Riverside Academy']);
    makeInstitution(['name' => 'Lakeside College', 'type' => 'College', 'is_approved' => false]);

    $response = $this->get(route('report.index'));

    $response->assertOk();
    $response->assertSee('Reports & Analytics');
    $response->assertSee('Riverside Academy');
    $response->assertSee('Pending Approvals');
    $response->assertSee('Needs Your Attention');
    $response->assertSee('Lakeside College');
    $response->assertSee('Growth');
    $response->assertSee('Fee Collection Trend');
    $response->assertSee('Institutions by Type');
});

test('a director only sees their own institution, not system-wide stats', function () {
    $director = User::factory()->create();
    $director->assignRole('Director');

    $institution = makeInstitution(['user_id' => $director->id]);
    $otherInstitution = makeInstitution(['name' => 'Someone Elses School']);

    $this->actingAs($director);

    $response = $this->get(route('report.index'));

    $response->assertOk();
    $response->assertDontSee('Needs Your Attention');
    $response->assertDontSee('Someone Elses School');
});

test('the admin can approve a pending institution from the dashboard', function () {
    $admin = makeAnalyticsAdmin();
    $this->actingAs($admin);

    $institution = makeInstitution(['is_approved' => false]);

    $response = $this->post(route('institution.approve', $institution));

    $response->assertRedirect();
    expect($institution->fresh()->is_approved)->toBeTrue();
});
