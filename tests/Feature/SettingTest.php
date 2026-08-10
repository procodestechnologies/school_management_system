<?php

use App\Models\Setting;

test('a feature with no stored row defaults to enabled', function () {
    expect(Setting::isEnabled('sms'))->toBeTrue();
});

test('set() persists the value and is reflected immediately, bypassing the cache', function () {
    Setting::set('sms', false);

    expect(Setting::isEnabled('sms'))->toBeFalse();
    expect(Setting::where('key', 'sms')->value('enabled'))->toBeFalse();

    Setting::set('sms', true);

    expect(Setting::isEnabled('sms'))->toBeTrue();
});
