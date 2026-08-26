<?php

use Database\Seeders\PermissionSeeder;

beforeEach(fn () => test()->seed(PermissionSeeder::class));

test('scratch dump', function () {
    [$director, $institution] = rcpSchool();
    $institution->update([
        'physical_address' => 'Garden Estate Road, Mukima Drive',
        'city' => 'Nairobi',
        'phone' => '+254 702 683 707',
        'email' => 'support@elimikasasa.co.ke',
    ]);
    rcpLoadScale($institution, null, 'cbc');

    file_put_contents(
        $_SERVER['PREVIEW_OUT'],
        $this->actingAs($director)->get(route('reportcard.settings.preview'))->getContent()
    );

    expect(true)->toBeTrue();
});
