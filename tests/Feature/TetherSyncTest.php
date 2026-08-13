<?php

use App\Models\User;

/**
 * Tether auto-registers POST /tether/push and /tether/pull, and the
 * package's own default middleware is ['api'] - open to anyone. These
 * endpoints write to and read from a multi-tenant database, so the
 * authentication configured in config/tether-server.php is load-bearing.
 * If a package upgrade or a re-publish of that config drops it, these
 * tests are what catches it.
 */
test('the sync endpoints reject unauthenticated callers', function () {
    $this->postJson(route('tether.push'))->assertUnauthorized();
    $this->postJson(route('tether.pull'))->assertUnauthorized();
});

test('a token holder gets past authentication', function () {
    $user = User::factory()->create();

    // An empty body fails validation rather than authentication - proof the
    // request reached the controller instead of being turned away.
    $this->actingAs($user, 'sanctum')
        ->postJson(route('tether.push'), [])
        ->assertStatus(422);
});

test('both sync routes carry authentication and throttling', function () {
    $routes = collect(app('router')->getRoutes()->getRoutes())
        ->filter(fn ($route) => str_starts_with($route->uri(), 'tether/'));

    expect($routes)->toHaveCount(2);

    $routes->each(function ($route) {
        expect($route->gatherMiddleware())
            ->toContain('auth:sanctum')
            ->toContain('throttle:sync');
    });
});
