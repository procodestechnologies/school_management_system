<?php

namespace App\Providers;

use App\Http\Controllers\DeviceProfileController;
use App\Listeners\ReconcileSyncedFeeBalances;
use App\Listeners\RecordDeviceSyncActivity;
use App\Models\SyncSetting;
use App\Models\User;
use GuzzleHttp\Middleware;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Modules\Curriculum\Models\Curriculum;
use Modules\FeeManagement\Models\Fee;
use Modules\FeeManagement\Models\FeePayment;
use Modules\Institution\Models\Institution;
use Modules\Parent\Models\ParentDetails;
use Modules\Staff\Models\StaffDetails;
use Modules\Staff\Models\StaffPayment;
use Modules\Student\Models\StudentDetails;
use Psr\Http\Message\RequestInterface;
use Tether\Client\ClientIdResolver;
use Tether\Client\SyncHttpClient;
use Tether\Core\Conflict\ConflictResolution;
use Tether\Core\Mutation\Mutation;
use Tether\Core\Sync\Snapshot;
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
        // Order matters: snapshots are generated and applied in this
        // sequence, and a device's foreign keys have to resolve as they
        // land. Reference data first, then the records that point at it.
        'App\Sync\Models\Curriculum' => [Curriculum::class, 'everything', true],
        'App\Sync\Models\User' => [User::class, 'schoolPeople', true],
        'App\Sync\Models\ParentDetails' => [ParentDetails::class, 'schoolParents', true],
        'App\Sync\Models\Institution' => [Institution::class, 'ownRow', true],
        'App\Sync\Models\StudentDetails' => [StudentDetails::class, 'byInstitution', true],
        'App\Sync\Models\StaffDetails' => [StaffDetails::class, 'byInstitution', false],
        'App\Sync\Models\Fee' => [Fee::class, 'byInstitution', false],
        'App\Sync\Models\FeePayment' => [FeePayment::class, 'byInstitution', false],
        'App\Sync\Models\StaffPayment' => [StaffPayment::class, 'byInstitution', false],
    ];

    public function register(): void
    {
        foreach (self::SYNCED_MODELS as $alias => [$modelClass]) {
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

        foreach (self::SYNCED_MODELS as $alias => [$modelClass, $scope, $readOnly]) {
            $registry->register(
                modelClass: $alias,
                scope: match ($scope) {
                    'everything' => null,
                    'ownRow' => self::scopeToOwnInstitutionRow(...),
                    'schoolPeople' => self::scopeToSchoolPeople(...),
                    'schoolParents' => self::scopeToSchoolParents(...),
                    default => self::scopeToInstitution(...),
                },
                pullSnapshotMapper: $modelClass === User::class ? self::stripCredentials(...) : null,
                // Reference data flows one way. A device that tried to push
                // a user or an institution is malfunctioning or hostile;
                // either way the server is not the place to find out.
                pushMutationMapper: $readOnly ? self::refuseClientWrites(...) : self::forceInstitution(...),
                conflictResolver: self::serverWinsOnConflict(...),
            );
        }

        $this->registerDeviceProfileRoute();

        Event::listen(PushSyncCompleted::class, ReconcileSyncedFeeBalances::class);
        Event::listen(PushSyncCompleted::class, RecordDeviceSyncActivity::class);
        Event::listen(PullSyncCompleted::class, RecordDeviceSyncActivity::class);
    }

    /**
     * Sits alongside Tether's own endpoints and behind the same guards: a
     * pairing device asks who it syncs as, using nothing but its token.
     */
    private function registerDeviceProfileRoute(): void
    {
        Route::middleware(config('tether-server.middleware', []))
            ->prefix(config('tether-server.route_prefix', 'tether'))
            ->get('device/profile', DeviceProfileController::class)
            ->name('tether.device.profile');
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
        // Pairing happens after installation, so what the device was told
        // then outranks anything baked into the build's config.
        ClientIdResolver::resolveUsing(
            fn (): string => (string) SyncSetting::get('client_id', (string) config('tether-client.client_id')),
        );

        // Same reasoning for the server address: it's learned at pairing,
        // long after this build's config was baked.
        if ($server = SyncSetting::get('server_url', (string) config('sync.server_url'))) {
            config([
                'tether-client.server_routes.push' => rtrim($server, '/').'/tether/push',
                'tether-client.server_routes.pull' => rtrim($server, '/').'/tether/pull',
            ]);
        }

        $token = (string) SyncSetting::get('device_token', (string) config('sync.device_token'));

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
     * The device's own school record, reached by primary key - institutions
     * have no institution_id of their own.
     */
    public static function scopeToOwnInstitutionRow(Builder $query, string $clientId, Request $request): Builder
    {
        return $query->whereKey(self::institutionIdFor($request->user()) ?? 0);
    }

    /**
     * The people a device needs to make sense of its own records: the
     * school's students and their parents, its staff, and its owner.
     * Everyone else on the platform stays on the server.
     */
    public static function scopeToSchoolPeople(Builder $query, string $clientId, Request $request): Builder
    {
        $institutionId = self::institutionIdFor($request->user()) ?? 0;

        return $query->where(function (Builder $people) use ($institutionId): void {
            $people
                ->whereIn('id', StudentDetails::where('institution_id', $institutionId)->select('student_id'))
                ->orWhereIn('id', StudentDetails::where('institution_id', $institutionId)->select('parent_id'))
                ->orWhereIn('id', StaffDetails::where('institution_id', $institutionId)->whereNotNull('user_id')->select('user_id'))
                ->orWhereIn('id', Institution::whereKey($institutionId)->select('user_id'));
        });
    }

    /**
     * Parent records belonging to this school's students.
     */
    public static function scopeToSchoolParents(Builder $query, string $clientId, Request $request): Builder
    {
        $institutionId = self::institutionIdFor($request->user()) ?? 0;

        return $query->whereIn('id', StudentDetails::where('institution_id', $institutionId)->select('parent_id'));
    }

    /**
     * A device needs to show who a fee belongs to; it does not need anyone's
     * credentials to do that. Password hashes, remember tokens and two-factor
     * secrets are replaced rather than removed - the column is not nullable,
     * and an unusable value means a synced account cannot be signed into on
     * the device. Only the paired account gets a local passcode.
     */
    public static function stripCredentials(Snapshot $snapshot, Model $row): Snapshot
    {
        return $snapshot->withPayload(array_merge($snapshot->payload, [
            'password' => 'no-local-login',
            'remember_token' => null,
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
        ]));
    }

    /**
     * Reference data is server-owned; a client may read it and nothing more.
     */
    public static function refuseClientWrites(Mutation $mutation, Request $request): Mutation
    {
        abort(403, 'This record type is read-only on devices.');
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
