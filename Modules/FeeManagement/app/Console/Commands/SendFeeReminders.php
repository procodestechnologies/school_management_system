<?php

namespace Modules\FeeManagement\Console\Commands;

use App\Services\FeeReminderService;
use Illuminate\Console\Command;
use Modules\FeeManagement\Models\Fee;

class SendFeeReminders extends Command
{
    protected $signature = 'feemanagement:send-reminders';

    protected $description = 'Remind parents by email and SMS of any outstanding fee balances, platform-wide';

    public function handle(FeeReminderService $reminderService): int
    {
        $result = $reminderService->sendForDefaulters(Fee::query());

        $this->info("Reminded {$result['parents_notified']} parent(s) - {$result['emails_sent']} email(s), {$result['sms_sent']} SMS.");

        return self::SUCCESS;
    }
}
