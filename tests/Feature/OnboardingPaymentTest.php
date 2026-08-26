<?php

use App\Models\Payment;
use App\Models\Plan;
use App\Models\Setting;
use App\Models\User;
use App\Services\PaystackService;
use Database\Seeders\PermissionSeeder;
use Illuminate\Support\Str;
use Modules\Institution\Actions\SaveInstitution;
use Modules\Institution\Models\Institution;

beforeEach(function () {
    $this->seed(PermissionSeeder::class);
    Setting::put(Setting::SETUP_FEE, '5000');
});

function onboardingPlan(array $overrides = []): Plan
{
    return Plan::create(array_merge([
        'name' => 'Standard',
        'slug' => 'standard-'.uniqid(),
        'price' => 2500,
        'billing_cycle' => 'monthly',
        'modules' => ['Student'],
        'features' => [],
        'is_active' => true,
    ], $overrides));
}

function onboardingDirector(): User
{
    $user = User::factory()->create();
    $user->assignRole('Director');

    return $user->refresh();
}

/**
 * Paystack, stubbed at the boundary. Returns a checkout URL on init and
 * reports whatever status the test asks for on verify.
 */
function fakePaystack(string $verifyStatus = 'success'): void
{
    test()->mock(PaystackService::class, function ($mock) use ($verifyStatus) {
        $mock->shouldReceive('initializeTransaction')->andReturn([
            'status' => true,
            'data' => ['authorization_url' => 'https://checkout.paystack.test/abc'],
        ]);
        $mock->shouldReceive('verifyTransaction')->andReturn([
            'data' => ['status' => $verifyStatus, 'id' => 'PS-1', 'channel' => 'card'],
        ]);
        $mock->shouldReceive('verifyWebhookSignature')->andReturn(true);
    });
}

/**
 * The setup fee, paid end to end, leaving an unclaimed payment behind.
 */
function paySetupFee(User $director, Plan $plan): Payment
{
    fakePaystack();

    test()->actingAs($director)->post(route('onboarding.pay'), ['plan_id' => $plan->id]);

    $payment = Payment::where('initiated_by', $director->id)->firstOrFail();

    test()->actingAs($director)->get(route('onboarding.callback', ['reference' => $payment->reference]));

    return $payment->fresh();
}

test('a director with no school is sent to pay the setup fee, not straight to registration', function () {
    $director = onboardingDirector();

    $this->actingAs($director)->get(route('dashboard'))->assertRedirect(route('onboarding.plan'));

    // And the registration form itself is not a way around it.
    $this->actingAs($director)->get(route('onboarding.plan'))->assertOk()->assertSee('Choose your plan');
});

test('paying the setup fee records a pending payment with no institution attached', function () {
    $director = onboardingDirector();
    $plan = onboardingPlan();

    fakePaystack();

    $this->actingAs($director)
        ->post(route('onboarding.pay'), ['plan_id' => $plan->id])
        ->assertRedirect('https://checkout.paystack.test/abc');

    $payment = Payment::firstOrFail();

    expect($payment->purpose)->toBe(Payment::PURPOSE_SETUP)
        ->and($payment->status)->toBe('pending')
        // The whole point: there is no school yet to attach it to.
        ->and($payment->institution_id)->toBeNull()
        ->and((float) $payment->amount)->toBe(5000.0)
        ->and($payment->plan_id)->toBe($plan->id);
});

test('a settled setup fee opens the registration step', function () {
    $director = onboardingDirector();
    $plan = onboardingPlan();

    $payment = paySetupFee($director, $plan);

    expect($payment->status)->toBe('successful')
        ->and($payment->paid_at)->not->toBeNull();

    // The gate now lets them through to register the school.
    $this->actingAs($director)->get(route('dashboard'))->assertRedirect(route('institution.create'));
});

test('an abandoned payment charges nothing and leaves the director where they started', function () {
    $director = onboardingDirector();
    $plan = onboardingPlan();

    fakePaystack(verifyStatus: 'failed');

    $this->actingAs($director)->post(route('onboarding.pay'), ['plan_id' => $plan->id]);

    $payment = Payment::firstOrFail();

    $this->actingAs($director)
        ->get(route('onboarding.callback', ['reference' => $payment->reference]))
        ->assertRedirect(route('onboarding.plan'));

    expect($payment->fresh()->status)->toBe('failed');

    // Still gated - a failed payment must not open the door.
    $this->actingAs($director)->get(route('dashboard'))->assertRedirect(route('onboarding.plan'));
});

test('registering the school claims the payment and remembers the plan chosen', function () {
    $director = onboardingDirector();
    $plan = onboardingPlan();

    $payment = paySetupFee($director, $plan);

    $institution = app(SaveInstitution::class)->create([
        'name' => 'Sample School',
        'code' => 'SS-'.uniqid(),
        'type' => 'School',
        'email' => 'head@sample.test',
        'phone' => '0712345678',
        'county' => 'Nairobi',
        'city' => 'Nairobi',
        'postal_address' => 'P.O. Box 1',
        'physical_address' => 'Somewhere Road',
    ], $director);

    expect($payment->fresh()->institution_id)->toBe($institution->id)
        // The plan is remembered, not granted: paying to be set up is not
        // paying for a period of service.
        ->and($institution->selected_plan_id)->toBe($plan->id)
        ->and($institution->subscription_plan)->toBeNull()
        ->and($institution->hasActiveSubscription())->toBeFalse();
});

test('a setup fee cannot be spent twice on a second school', function () {
    $director = onboardingDirector();
    $plan = onboardingPlan();

    paySetupFee($director, $plan);

    $make = fn (string $name) => app(SaveInstitution::class)->create([
        'name' => $name,
        'code' => 'C-'.uniqid(),
        'type' => 'School',
        'email' => Str::slug($name).'@sample.test',
        'phone' => '0712345678',
        'county' => 'Nairobi',
        'city' => 'Nairobi',
        'postal_address' => 'P.O. Box 1',
        'physical_address' => 'Somewhere Road',
    ], $director);

    $first = $make('First School');
    $second = $make('Second School');

    expect(Payment::where('institution_id', $first->id)->count())->toBe(1)
        // Nothing left unclaimed for the second one to take.
        ->and(Payment::where('institution_id', $second->id)->count())->toBe(0)
        ->and($second->selected_plan_id)->toBeNull();
});

test('with no setup fee configured, onboarding stays the single step it always was', function () {
    Setting::put(Setting::SETUP_FEE, '0');

    $director = onboardingDirector();

    expect(Setting::setupFee())->toBe(0.0);

    $this->actingAs($director)->get(route('dashboard'))->assertRedirect(route('institution.create'));
});

test('choosing a plan with no fee to pay skips checkout entirely', function () {
    Setting::put(Setting::SETUP_FEE, '0');

    $director = onboardingDirector();
    $plan = onboardingPlan();

    $this->actingAs($director)
        ->post(route('onboarding.pay'), ['plan_id' => $plan->id])
        ->assertRedirect(route('institution.create'));

    // Nobody is sent to a checkout for nothing.
    expect(Payment::count())->toBe(0);
});

test('a quoted plan cannot be checked out, since there is no price to charge', function () {
    $director = onboardingDirector();
    $premium = onboardingPlan(['name' => 'Premium', 'price' => 0, 'is_custom_priced' => true]);

    $this->actingAs($director)
        ->post(route('onboarding.pay'), ['plan_id' => $premium->id])
        ->assertRedirect();

    expect(Payment::count())->toBe(0);
});

test('the webhook settles a setup fee without trying to start a subscription', function () {
    $director = onboardingDirector();
    $plan = onboardingPlan();

    fakePaystack();

    $this->actingAs($director)->post(route('onboarding.pay'), ['plan_id' => $plan->id]);
    $payment = Payment::firstOrFail();

    // The browser never comes back; Paystack tells the server instead.
    $this->postJson(route('billing.webhook'), [
        'event' => 'charge.success',
        'data' => ['reference' => $payment->reference],
    ])->assertNoContent();

    expect($payment->fresh()->status)->toBe('successful')
        ->and($payment->fresh()->institution_id)->toBeNull();
});

test('a director who already runs a school is not sent back through onboarding', function () {
    $director = onboardingDirector();

    Institution::create([
        'user_id' => $director->id,
        'name' => 'Existing School',
        'code' => 'EX-'.uniqid(),
        'type' => 'School',
        'is_active' => true,
        'status' => 'active',
        'is_approved' => true,
    ]);

    $this->actingAs($director)->get(route('onboarding.plan'))->assertRedirect(route('dashboard'));
});
