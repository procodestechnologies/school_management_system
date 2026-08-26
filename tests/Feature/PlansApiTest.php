<?php

use App\Models\Plan;

function makePlan(array $overrides = []): Plan
{
    return Plan::create(array_merge([
        'name' => 'Starter',
        'slug' => 'starter-'.uniqid(),
        'description' => 'For a school finding its feet.',
        'price' => 15000,
        'billing_cycle' => 'monthly',
        'modules' => ['Student', 'FeeManagement', 'ReportCard'],
        'features' => ['ai_receipt_scanning'],
        'is_active' => true,
    ], $overrides));
}

test('anyone can list the plans on sale, without signing in', function () {
    $growth = makePlan(['name' => 'Growth', 'price' => 35000]);
    $starter = makePlan(['name' => 'Starter', 'price' => 15000]);

    $response = $this->getJson('/api/plans');

    $response->assertOk();

    // Cheapest first, so a pricing page can render them in order as given.
    expect($response->json('data.*.id'))->toBe([$starter->id, $growth->id])
        ->and($response->json('data.*.name'))->toBe(['Starter', 'Growth']);
});

test('a plan carries its price, cycle, modules and features, each labelled', function () {
    $plan = makePlan();

    $data = $this->getJson('/api/plans')->assertOk()->json('data.0');

    expect($data['id'])->toBe($plan->id)
        ->and($data['slug'])->toBe($plan->slug)
        ->and($data['name'])->toBe('Starter')
        ->and($data['description'])->toBe('For a school finding its feet.')
        // A number rather than the decimal string the column holds, so a
        // frontend can format it without parsing it first.
        ->and(is_string($data['price']))->toBeFalse()
        ->and((float) $data['price'])->toBe(15000.0)
        ->and($data['currency'])->toBe('KES')
        ->and($data['billing_cycle'])->toBe('monthly')
        // Each entry pairs the stable key with wording fit to print.
        ->and($data['modules'])->toBe([
            ['key' => 'Student', 'label' => 'Student'],
            ['key' => 'FeeManagement', 'label' => 'Fee Management'],
            ['key' => 'ReportCard', 'label' => 'Report Card'],
        ])
        ->and($data['features'])->toBe([
            ['key' => 'ai_receipt_scanning', 'label' => 'AI Receipt Scanning (Fee Management)'],
        ]);
});

test('the public shape carries nothing beyond what a pricing page needs', function () {
    makePlan();

    $data = $this->getJson('/api/plans')->assertOk()->json('data.0');

    // Named explicitly: a column added to the plans table later must not
    // appear here until someone puts it in PlanResource on purpose.
    expect(array_keys($data))->toBe([
        'id', 'slug', 'name', 'description', 'price', 'currency', 'billing_cycle', 'is_featured', 'modules', 'features',
    ]);
});

test('a plan withdrawn from sale is absent from the list and unreachable directly', function () {
    $retired = makePlan(['name' => 'Retired', 'is_active' => false]);
    makePlan(['name' => 'Live']);

    expect($this->getJson('/api/plans')->assertOk()->json('data.*.name'))->toBe(['Live']);

    // A 404 rather than a 403: the public has no business learning it exists.
    $this->getJson('/api/plans/'.$retired->slug)->assertNotFound();
    $this->getJson('/api/plans/'.$retired->id)->assertNotFound();
});

test('a single plan is fetchable by slug or by id', function () {
    $plan = makePlan();

    $bySlug = $this->getJson('/api/plans/'.$plan->slug)->assertOk()->json('data');
    $byId = $this->getJson('/api/plans/'.$plan->id)->assertOk()->json('data');

    expect($bySlug)->toBe($byId)
        ->and($bySlug['name'])->toBe('Starter')
        ->and($bySlug['modules'])->toHaveCount(3);
});

test('an unknown plan is a json 404, not an html error page', function () {
    $response = $this->getJson('/api/plans/no-such-plan');

    $response->assertNotFound();
    $response->assertHeader('content-type', 'application/json');
});

test('a plan with no modules or features returns empty lists rather than null', function () {
    // The columns are nullable, and a frontend mapping over them shouldn't
    // have to guard against null.
    makePlan(['modules' => [], 'features' => null]);

    $data = $this->getJson('/api/plans')->assertOk()->json('data.0');

    expect($data['modules'])->toBe([])
        ->and($data['features'])->toBe([]);
});

test('the endpoints answer cross-origin, which is the point of them', function () {
    makePlan();

    $response = $this->getJson('/api/plans', ['Origin' => 'https://marketing.example.com']);

    $response->assertOk();
    $response->assertHeader('access-control-allow-origin', '*');
});

test('the public pricing page is rendered from the plans table, not from the template', function () {
    makePlan([
        'name' => 'Growth',
        'description' => 'For a school running everything in one place.',
        'price' => 35000,
        'billing_cycle' => 'monthly',
        'modules' => ['FeeManagement', 'Timetable'],
        'features' => ['ai_receipt_scanning'],
        'is_featured' => true,
    ]);
    makePlan(['name' => 'Retired', 'is_active' => false]);

    $response = $this->get(route('plans'));

    $response->assertOk()
        ->assertSee('Growth')
        ->assertSee('For a school running everything in one place.')
        ->assertSee('KES 35,000')
        ->assertSee('/month')
        // What the plan grants, spelled out from its own modules/features.
        ->assertSee('Fee Management')
        ->assertSee('Timetable')
        ->assertSee('AI Receipt Scanning (Fee Management)')
        // The badge follows the flag rather than the card's position.
        ->assertSee('Most popular')
        // A plan withdrawn from sale is off the page entirely.
        ->assertDontSee('Retired')
        // And none of the copy the template used to hardcode survives.
        ->assertDontSee('Up to 300 students')
        ->assertDontSee('Everything in Starter');
});

test('a free plan reads as free, and a page with no plans says so', function () {
    makePlan(['name' => 'Basic', 'price' => 0]);

    $this->get(route('plans'))->assertOk()->assertSee('Free')->assertDontSee('KES 0');

    Plan::query()->update(['is_active' => false]);

    $this->get(route('plans'))
        ->assertOk()
        ->assertSee("We're putting together pricing for the coming term.", false);
});
