<?php

use App\Models\User;
use Database\Seeders\PermissionSeeder;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('an onboarded user can visit the dashboard', function () {
    // The dashboard gates on the 'view dashboard' permission, and
    // HasInstitution sends anyone who might own a school through onboarding
    // first - a Parent is the simplest role that clears both.
    $this->seed(PermissionSeeder::class);

    $user = User::factory()->create();
    $user->assignRole('Parent');
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    $response->assertOk();
});

test('a user who has not verified their email is sent to verify it', function () {
    $user = User::factory()->unverified()->create();
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));

    $response->assertRedirect(route('verification.notice'));
});

test('a suspended user is logged out', function () {
    $user = User::factory()->suspended()->create();
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));

    $response->assertRedirect(route('login'));
    expect(auth()->check())->toBeFalse();
});

test('a user without an institution is sent through onboarding', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('institution.create'));
});
