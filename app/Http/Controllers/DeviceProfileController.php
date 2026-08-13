<?php

namespace App\Http\Controllers;

use App\Providers\TetherServiceProvider;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Institution\Models\Institution;

/**
 * Tells a pairing device who it syncs as, so the device can build a local
 * account and let that person sign in with no connection.
 *
 * Note what is *not* here: the account's password hash. A device only
 * ever learns the identity and the roles; the passcode used to unlock the
 * offline app is chosen on the device and never leaves it. That way a
 * stolen laptop yields a passcode hash for that one device rather than a
 * crackable copy of a credential that also works on the live server.
 */
class DeviceProfileController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $user = $request->user();

        $institutionId = TetherServiceProvider::institutionIdFor($user);

        abort_if($institutionId === null, 403, 'This account is not linked to a school.');

        $institution = Institution::find($institutionId);

        return response()->json([
            'user' => [
                // The id is carried across deliberately: local rows must
                // line up with server rows for institution scoping and
                // ownership to mean the same thing on both sides.
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'roles' => $user->getRoleNames()->values(),
            ],
            'institution' => [
                'id' => $institution?->id,
                'name' => $institution?->name,
                'code' => $institution?->code,
            ],
            'server_time' => now()->toIso8601String(),
        ]);
    }
}
