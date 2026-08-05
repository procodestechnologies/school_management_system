<?php

use App\Models\ContactMessage;
use App\Models\User;
use Spatie\Permission\Models\Role;

function makeAdmin(): User
{
    Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);

    $user = User::factory()->create();
    $user->assignRole('Admin');

    return $user;
}

test('guests are redirected to the login page', function () {
    $response = $this->get(route('messages.index'));

    $response->assertRedirect(route('login'));
});

test('non-admins cannot view messages', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('messages.index'));

    $response->assertForbidden();
});

test('admins can view the messages list', function () {
    $admin = makeAdmin();
    $this->actingAs($admin);

    ContactMessage::factory()->create(['name' => 'Jane Wanjiru', 'topic' => 'sales']);

    $response = $this->get(route('messages.index'));

    $response->assertOk();
    $response->assertSee('Jane Wanjiru');
});

test('admins can view a single message', function () {
    $admin = makeAdmin();
    $this->actingAs($admin);

    $message = ContactMessage::factory()->create([
        'name' => 'Jane Wanjiru',
        'message' => 'We would like to bring our school onto the platform.',
    ]);

    $response = $this->get(route('messages.show', $message));

    $response->assertOk();
    $response->assertSee('Jane Wanjiru');
    $response->assertSee('We would like to bring our school onto the platform.');
});
