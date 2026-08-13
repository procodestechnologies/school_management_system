<?php

namespace App\Providers;

use App\Listeners\ReconcileSyncedFeeBalances;
use App\Listeners\RecordDeviceSyncActivity;
use App\Models\User;
use GuzzleHttp\Middleware;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Modules\FeeManagement\Models\Fee;
use Modules\FeeManagement\Models\FeePayment;
use Modules\Staff\Models\StaffDetails;
use Modules\Staff\Models\StaffPayment;
use Psr\Http\Message\RequestInterface;
use Tether\Client\ClientIdResolver;
use Tether\Client\SyncHttpClient;
use Tether\Core\Conflict\ConflictResolution;
use Tether\Core\Mutation\Mutation;
use Tether\Server\Events\PullSyncCompleted;
use Tether\Server\Events\PushSyncCompleted;
use Tether\Server\SyncRegistry;

/**
 * Wires the application's models into Tether's sync engine.
 *
 * Two things make this less direct than the package's documented setup:
 *
 * 1. Tether resolves an inbound push mutation's model as
 *    `config('tether-server.model_namespace') . '\' . class_basename($model)`
 *    - one flat namespace - while pull iterates the registry's own class
 *    keys. This app's models live in Modules\<Module>\Models\*, so those
 *    two names would disagree: pull would work, push would fail to resolve
 *    the class, and the registry lookups for the push guard and conflict
 *    resolver would quietly return null while writes still applied. Each
 *    model is therefore aliased into one namespace (App\Sync\Models) and
 *    registered under that alias, so push and pull agree on the name.
 *    class_alias makes the alias the *same* class, so tables, casts and
 *    relations are untouched.
 *
 * 2. Every registration is scoped to the requesting device's institution.
 *    Without that, one school's device could pull another school's records.
 */
class TetherServiceProvider extends ServiceProvider
{
    /**
     * Models exposed to offline clients, keyed by the alias they're
     * registered and pushed under.
     *
     * @var array<string, class-string<Model>>
     */
    private const SYNCED_MODELS = [
        'App\Sync\Models\Fee' => Fee::class,
        'App\Sync\Models\FeePayment' => FeePayment::class,
        'App\Sync\Models\StaffDetails' => StaffDetails::class,
        'App\Sync\Models\StaffPayment' => StaffPayment::class,
    ];

    public function register(): void
    {
        foreach (self::SYNCED_MODELS as $alias => $modelClass) {
            if (! class_exists($alias, false)) {
                class_alias($modelClass, $alias);
            }
        }
    }

    public function boot(): void
    {
        if (syncClientMode()) {
            $this->bootAsClient();
        }

        $registry = $this->app->make(SyncRegistry::class);

        foreach (array_keys(self::SYNCED_MODELS) as $alias) {
            $registry->register(
                modelClass: $alias,
                scope: self::scopeToInstitution(...),
                pushMutationMapper: self::forceInstitution(...),
                conflictResolver: self::serverWinsOnConflict(...),
            );
        }

        Event::listen(PushSyncCompleted::class, ReconcileSyncedFeeBalances::class);
        Event::listen(PushSyncCompleted::class, RecordDeviceSyncActivity::class);
        Event::listen(PullSyncCompleted::class, RecordDeviceSyncActivity::class);
    }

    /**
     * Client-side wiring: identify this device to the server and sign every
     * outbound sync request with its token.
     *
     * The package's HTTP client takes no credentials of its own - middleware
     * is the documented seam for auth - so without this a device would reach
     * the server anonymously and be turned away by auth:sanctum.
     */
    private function bootAsClient(): void
    {
        ClientIdResolver::resolveUsing(fn (): string => (string) config('tether-client.client_id'));

        $token = (string) config('sync.device_token');

        if ($token === '') {
            return;
        }

        $this->app->resolving(SyncHttpClient::class, function (SyncHttpClient $client) use ($token): void {
            $client->withMiddleware(Middleware::mapRequest(
                fn (RequestInterface $request): RequestInterface => $request->withHeader('Authorization', 'Bearer '.$token),
            ));
        });
    }

    /**
     * A device only ever pulls its own school's rows.
     */
    public static function scopeToInstitution(Builder $query, string $clientId, Request $request): Builder
    {
        return $query->where('institution_id', self::institutionIdFor($request->user()) ?? 0);
    }

    /**
     * ...and only ever writes into its own school, whatever institution_id
     * the payload claims. A device with no resolvable institution can't
     * sync at all rather than writing somewhere arbitrary.
     */
    public static function forceInstitution(Mutation $mutation, Request $request): Mutation
    {
        $institutionId = self::institutionIdFor($request->user());

        abort_if($institutionId === null, 403, 'This account is not linked to a school, so it cannot sync.');

        return $mutation->withPayload(
            array_merge($mutation->getPayload(), ['institution_id' => $institutionId]),
        );
    }

    /**
     * These records are money. Tether's default already lets the newer
     * write win and only treats an *older* mutation as a conflict, so
     * reaching here means the server moved on after this device went
     * offline - the server copy stands and the device re-pulls it, rather
     * than a stale phone silently overwriting a recorded payment.
     */
    public static function serverWinsOnConflict(Mutation $mutation, Model $record, Request $request): ConflictResolution
    {
        return ConflictResolution::reject();
    }

    /**
     * The school a sync client belongs to. Mirrors currentInstitution(),
     * but reads from the token's user rather than the session.
     */
    public static function institutionIdFor(?User $user): ?int
    {
        if (! $user) {
            return null;
        }

        if ($user->hasRole('Accountant')) {
            return $user->staffUserDetails?->institution_id;
        }

        return $user->active_institution_id;
    }
}
