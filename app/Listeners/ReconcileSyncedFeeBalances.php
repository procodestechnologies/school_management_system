<?php

namespace App\Listeners;

use App\Providers\TetherServiceProvider;
use Modules\FeeManagement\Models\Fee;
use Tether\Server\Events\PushSyncCompleted;

/**
 * Fee.amount_paid is the aggregate everything else in the app reads -
 * dashboards, reminders, the balance and status accessors - and normally
 * FeePaymentService keeps it in step when a payment is recorded.
 *
 * Mutations arriving from an offline device never go through that service:
 * Tether writes the FeePayment row straight to the database, inside
 * withoutEvents. Without this listener a synced payment would land in the
 * ledger while the fee still showed the old balance.
 *
 * Recomputing from the payments themselves (rather than incrementing) is
 * deliberate - it's idempotent, so a device that retries a push, or two
 * devices that both recorded against the same fee, still converge on the
 * right figure instead of double-counting.
 */
class ReconcileSyncedFeeBalances
{
    public function handle(PushSyncCompleted $event): void
    {
        $institutionId = TetherServiceProvider::institutionIdFor($event->httpRequest->user());

        if ($institutionId === null) {
            return;
        }

        Fee::where('institution_id', $institutionId)
            ->withSum('payments', 'amount')
            ->get()
            ->each(function (Fee $fee): void {
                $recorded = round((float) ($fee->payments_sum_amount ?? 0), 2);

                if ((float) $fee->amount_paid === $recorded) {
                    return;
                }

                $fee->forceFill(['amount_paid' => $recorded])->saveQuietly();
            });
    }
}
