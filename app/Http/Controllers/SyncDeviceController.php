<?php

namespace App\Http\Controllers;

use App\Models\SyncDevice;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Enrolls the offline clients that sync through Tether, and cuts them off
 * again. Each enrolment mints one Sanctum token limited to the 'sync'
 * ability, so a leaked device token can't be turned on the rest of the
 * application.
 */
class SyncDeviceController extends Controller
{
    /**
     * The one ability a device token is granted. The Tether routes require
     * it, so this token is useless anywhere else.
     */
    public const SYNC_ABILITY = 'sync';

    public function index()
    {
        abort_unless(Auth::user()->can('view syncdevice'), 403);

        $query = SyncDevice::with(['user', 'institution'])->latest();

        if (! isAdmin()) {
            $query->where('institution_id', currentInstitution()?->id ?? 0);
        }

        return view('layouts.sync-devices.index', [
            'devices' => $query->paginate(10)->withQueryString(),
        ]);
    }

    public function create()
    {
        abort_unless(Auth::user()->can('create syncdevice'), 403);

        return view('layouts.sync-devices.create', [
            'accounts' => $this->eligibleAccounts(),
        ]);
    }

    public function store(Request $request)
    {
        abort_unless(Auth::user()->can('create syncdevice'), 403);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'platform' => 'required|in:desktop,android,ios',
            'user_id' => 'required|exists:users,id',
        ]);

        $institutionId = currentInstitution()?->id;

        abort_unless($institutionId || isAdmin(), 422, 'No institution selected.');

        // The device inherits its account's reach, so that account has to
        // be one this school actually controls - not any id that happens
        // to exist.
        $account = $this->eligibleAccounts()->firstWhere('id', (int) $validated['user_id']);

        abort_unless($account, 403, 'That account cannot be used for this school.');

        $plainTextToken = DB::transaction(function () use ($validated, $account, $institutionId) {
            $token = $account->createToken(
                name: 'sync-device: '.$validated['name'],
                abilities: [self::SYNC_ABILITY],
            );

            SyncDevice::create([
                'institution_id' => $institutionId ?? $account->active_institution_id,
                'user_id' => $account->id,
                'name' => $validated['name'],
                'platform' => $validated['platform'],
                'client_id' => (string) Str::ulid(),
                'token_id' => $token->accessToken->getKey(),
            ]);

            return $token->plainTextToken;
        });

        // Shown once and never stored in readable form - Sanctum only
        // keeps a hash, so a lost token means enrolling again.
        return redirect()->route('sync-devices.index')
            ->with('success', 'Device enrolled. Copy its token now - it will not be shown again.')
            ->with('device_token', $plainTextToken);
    }

    /**
     * Revoke a device: the token goes, the record stays so the history of
     * what was enrolled survives.
     */
    public function destroy(SyncDevice $syncDevice)
    {
        abort_unless(Auth::user()->can('delete syncdevice'), 403);

        $this->authorizeAccessTo($syncDevice);

        DB::transaction(function () use ($syncDevice) {
            $syncDevice->token?->delete();

            $syncDevice->update([
                'token_id' => null,
                'revoked_at' => now(),
            ]);
        });

        return redirect()->route('sync-devices.index')
            ->with('success', 'Device revoked. It can no longer sync.');
    }

    /**
     * Accounts a device may sync as: the school's Director and any
     * Accountant attached to it. Everyone else either has no business
     * syncing or isn't scoped to a single school.
     *
     * @return Collection<int, User>
     */
    private function eligibleAccounts()
    {
        $institutionId = currentInstitution()?->id;

        if (! $institutionId) {
            return collect();
        }

        return User::query()
            ->where(fn ($query) => $query
                ->where('active_institution_id', $institutionId)
                ->orWhereHas('staffUserDetails', fn ($staff) => $staff->where('institution_id', $institutionId)))
            ->get()
            ->filter(fn (User $user) => $user->hasAnyRole(['Director', 'Accountant']))
            ->values();
    }

    private function authorizeAccessTo(SyncDevice $device): void
    {
        if (isAdmin()) {
            return;
        }

        abort_unless($device->institution_id === currentInstitution()?->id, 403);
    }
}
