<?php

use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Modules\FeeManagement\Models\Fee;
use Modules\Institution\Models\Institution;
use Tether\Client\Models\MutationLog;

beforeEach(function () {
    $this->seed(PermissionSeeder::class);
});

function makeClientModeFee(): Fee
{
    $owner = User::factory()->create();
    $owner->assignRole('Director');

    $institution = Institution::create([
        'user_id' => $owner->id,
        'name' => 'School '.uniqid(),
        'code' => 'INST-'.uniqid(),
        'type' => 'School',
        'is_active' => true,
        'status' => 'active',
        'is_approved' => true,
    ]);

    $student = User::factory()->create();

    return Fee::create([
        'institution_id' => $institution->id,
        'student_id' => $student->id,
        'title' => 'Term 1 Tuition',
        'fee_type' => 'tuition',
        'amount' => 10000,
        'amount_paid' => 0,
    ]);
}

/**
 * The whole design rests on this: the same codebase is the server and the
 * offline client, and only a config flag separates them. If the server
 * ever started logging mutations it would accumulate a queue of writes
 * nobody drains, so this is the test that matters most.
 */
test('the server records no mutations', function () {
    expect(config('sync.client_mode'))->toBeFalse();

    $fee = makeClientModeFee();
    $fee->update(['title' => 'Revised Tuition']);
    $fee->delete();

    expect(MutationLog::count())->toBe(0);
});

test('a client records create, update and delete', function () {
    config()->set('sync.client_mode', true);

    $fee = makeClientModeFee();
    $fee->update(['title' => 'Revised Tuition']);
    $fee->delete();

    // Filtered to this fee: the institution and users the fixture creates
    // are syncable too, so they log their own mutations alongside it.
    $logged = MutationLog::where('entity_id', $fee->tether_id)->orderBy('id')->get();

    expect($logged)->toHaveCount(3)
        ->and($logged->pluck('operation')->map->value->all())->toBe(['create', 'update', 'delete']);
});

test('a logged mutation carries only the syncable fields', function () {
    config()->set('sync.client_mode', true);

    $fee = makeClientModeFee();

    $payload = MutationLog::where('entity_id', $fee->tether_id)->firstOrFail()->payload;

    // amount_paid is derived from payments and recomputed server-side, so
    // a device must never be the thing that reports it.
    expect($payload)->toHaveKey('title')
        ->and($payload)->toHaveKey('amount')
        ->and($payload)->not->toHaveKey('amount_paid');
});

test('the client is configured from a single server address', function () {
    config()->set('sync.client_mode', true);

    expect(config('tether-client.model_namespace'))->toBe('App\Sync\Models');

    // Both endpoints derive from TETHER_SERVER_URL rather than being set
    // separately, so a device can't be half-pointed at the wrong host.
    expect(config('tether-client.server_routes.push'))->toEndWith('/tether/push')
        ->and(config('tether-client.server_routes.pull'))->toEndWith('/tether/pull');
});
