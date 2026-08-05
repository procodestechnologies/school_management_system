<?php

use App\Mail\ContactMessageReceived;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;

test('the marketing pages are reachable', function (string $route) {
    $response = $this->get(route($route));

    $response->assertOk();
})->with(['home', 'about', 'services', 'plans', 'contact']);

test('a visitor can submit the contact form', function () {
    Mail::fake();

    Livewire::test('contact-form')
        ->set('name', 'Jane Wanjiru')
        ->set('email', 'jane@example.com')
        ->set('phone', '0700000000')
        ->set('topic', 'sales')
        ->set('message', 'We would like to bring our school onto the platform.')
        ->call('submit')
        ->assertHasNoErrors()
        ->assertSet('sent', true);

    $this->assertDatabaseHas('contact_messages', [
        'name' => 'Jane Wanjiru',
        'email' => 'jane@example.com',
        'topic' => 'sales',
    ]);

    Mail::assertSent(ContactMessageReceived::class, function (ContactMessageReceived $mail) {
        return $mail->contactMessage->email === 'jane@example.com';
    });
});

test('the contact form requires the essentials', function () {
    Livewire::test('contact-form')
        ->call('submit')
        ->assertHasErrors(['name', 'email', 'topic', 'message']);

    $this->assertDatabaseCount('contact_messages', 0);
});

test('the contact form rejects an invalid topic', function () {
    Livewire::test('contact-form')
        ->set('name', 'Jane Wanjiru')
        ->set('email', 'jane@example.com')
        ->set('topic', 'not-a-real-topic')
        ->set('message', 'Hello there, this is a test message.')
        ->call('submit')
        ->assertHasErrors('topic');
});

test('a filled honeypot silently drops the submission', function () {
    Mail::fake();

    Livewire::test('contact-form')
        ->set('name', 'Bot')
        ->set('email', 'bot@example.com')
        ->set('topic', 'sales')
        ->set('message', 'This is definitely not spam, please click my link.')
        ->set('companyWebsite', 'https://spam.example.com')
        ->call('submit')
        ->assertSet('sent', true);

    $this->assertDatabaseCount('contact_messages', 0);
    Mail::assertNothingSent();
});

test('too many submissions from the same visitor are rate limited', function () {
    Mail::fake();

    $component = Livewire::test('contact-form');

    foreach (range(1, 5) as $attempt) {
        $component->set('name', 'Jane Wanjiru')
            ->set('email', 'jane@example.com')
            ->set('topic', 'sales')
            ->set('message', 'We would like to bring our school onto the platform.')
            ->call('submit');
    }

    $component->set('name', 'Jane Wanjiru')
        ->set('email', 'jane@example.com')
        ->set('topic', 'sales')
        ->set('message', 'One message too many for this minute.')
        ->call('submit')
        ->assertHasErrors('message');

    $this->assertDatabaseCount('contact_messages', 5);
});
