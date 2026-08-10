<?php

namespace Modules\ReportCard\Console\Commands;

use App\Services\SmsService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Modules\ReportCard\Mail\ReportCardMail;
use Modules\ReportCard\Models\ReportCard;
use Modules\ReportCard\Services\ReportCardCompletionService;
use Modules\ReportCard\Services\ReportCardPdfService;

class SendReadyReportCards extends Command
{
    protected $signature = 'reportcards:send-ready';

    protected $description = 'Send parents a one-time download link (email and SMS) for report cards that have been ready for at least a day, giving directors time to correct results before parents see them';

    public function handle(
        ReportCardCompletionService $completionService,
        ReportCardPdfService $pdfService,
        SmsService $smsService,
    ): int {
        $reportCards = ReportCard::where('status', 'ready')
            ->where('completed_at', '<=', now()->subDay())
            ->with(['student', 'institution'])
            ->get();

        $sent = 0;
        $skipped = 0;

        foreach ($reportCards as $reportCard) {
            if (! $reportCard->student || ! $reportCard->academic_year || ! $completionService->isStillComplete($reportCard->student, $reportCard->term, $reportCard->academic_year)) {
                $reportCard->delete();
                $skipped++;

                continue;
            }

            // Resolved through the parent's own User/ParentDetails records -
            // student_details has no parent_email/parent_phone columns of
            // its own, despite the model listing them as fillable.
            $parent = $reportCard->student->studentParent;

            $email = featureEnabled('email_notifications') ? $parent?->email : null;
            $phone = featureEnabled('sms') ? $parent?->parent?->parent_phone : null;

            if (! $email && ! $phone) {
                $skipped++;

                continue;
            }

            $pdfService->generate($reportCard);

            $downloadUrl = route('reportcard.download', $reportCard->issueDownloadToken());

            if ($email) {
                Mail::to($email)->send(new ReportCardMail(
                    $reportCard,
                    $reportCard->student,
                    $reportCard->institution,
                    $downloadUrl,
                ));
            }

            if ($phone) {
                $sms = $smsService->send(
                    (int) preg_replace('/\D/', '', $phone),
                    "{$reportCard->student->name}'s report card for {$reportCard->term} is ready. Download it here (link works once): {$downloadUrl}",
                );

                if (! ($sms['success'] ?? false)) {
                    Log::warning('Report card SMS failed', [
                        'report_card_id' => $reportCard->id,
                        'error' => $sms['error'] ?? $sms['reason'] ?? null,
                    ]);
                }
            }

            $reportCard->update(['status' => 'sent', 'sent_at' => now()]);
            $sent++;
        }

        $this->info("Sent {$sent} report card(s), skipped {$skipped}.");

        return self::SUCCESS;
    }
}
