<?php

use App\Models\Setting;
use App\Services\SmsService;

test('sending is skipped without a network call when the sms feature is disabled', function () {
    Setting::set('sms', false);

    $response = (new SmsService)->send(254712345678, 'Test message');

    expect($response)->toBe([
        'success' => false,
        'error' => 'SMS is currently disabled in site settings',
    ]);
});

test('an unconfigured provider comes back as a failure rather than throwing', function () {
    Setting::set('sms', true);
    config()->set('sms.default', 'nope');

    $response = (new SmsService)->send(254712345678, 'Test message');

    expect($response['success'])->toBeFalse()
        ->and($response['error'])->toContain('nope');
});

/**
 * The gateway signals its own outcome via `status`, not `success` - every
 * caller branches on `success`, so the two must stay in step.
 */
test('a gateway response is normalised to carry a success flag', function () {
    $service = new SmsService;

    expect($service->normaliseResponse(['status' => 'success', 'transactionId' => '123']))
        ->toMatchArray(['success' => true, 'transactionId' => '123'])
        ->and($service->normaliseResponse(['status' => 'error', 'reason' => 'invalid mobile'])['success'])->toBeFalse()
        ->and($service->normaliseResponse(['reason' => 'nothing useful'])['success'])->toBeFalse();
});
