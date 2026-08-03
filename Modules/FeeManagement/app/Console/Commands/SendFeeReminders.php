<?php

namespace Modules\FeeManagement\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Modules\FeeManagement\Mail\FeeReminderMail;
use Modules\FeeManagement\Models\Fee;

class SendFeeReminders extends Command
{
    protected $signature = 'feemanagement:send-reminders';

    protected $description = 'Email parents a consolidated reminder of any outstanding fee balances';

    public function handle(): int
    {
        $outstandingFees = Fee::whereColumn('amount_paid', '<', 'amount')
            ->whereNotNull('parent_id')
            ->with('student')
            ->get();

        $sent = 0;

        foreach ($outstandingFees->groupBy('parent_id') as $parentId => $fees) {
            $parent = User::find($parentId);

            if (! $parent || ! $parent->email) {
                continue;
            }

            Mail::to($parent->email)->send(new FeeReminderMail($parent, $fees));
            $sent++;
        }

        $this->info("Sent fee reminders to {$sent} parent(s).");

        return self::SUCCESS;
    }
}
