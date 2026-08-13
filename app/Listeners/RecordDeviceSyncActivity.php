<?php

namespace App\Listeners;

use App\Models\SyncDevice;
use Illuminate\Http\Request;
use Tether\Server\Events\PullSyncCompleted;
use Tether\Server\Events\PushSyncCompleted;

/**
 * Stamps when each enrolled device last synced.
 *
 * This is what makes the device list actionable: without a last-seen
 * time, deciding which of a school's laptops to revoke is guesswork.
 */
class RecordDeviceSyncActivity
{
    public function handle(PushSyncCompleted|PullSyncCompleted $event): void
    {
        $clientId = $event instanceof PushSyncCompleted
            ? $event->pushRequest->clientId
            : $event->pullRequest->clientId;

        $this->stamp($clientId, $event->httpRequest);
    }

    private function stamp(?string $clientId, Request $request): void
    {
        if (blank($clientId)) {
            return;
        }

        SyncDevice::where('client_id', $clientId)
            ->where('user_id', $request->user()?->id)
            ->update([
                'last_synced_at' => now(),
                'last_seen_ip' => $request->ip(),
            ]);
    }
}
