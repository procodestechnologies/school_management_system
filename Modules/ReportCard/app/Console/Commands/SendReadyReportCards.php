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
use Throwable;

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
                // Caught rather than allowed to escape: the SMS below is
                // what reaches a parent whose inbox doesn't, so a bounced
                // or misconfigured mailer must not take it down with it.
                try {
                    Mail::to($email)->send(new ReportCardMail(
                        $reportCard,
                        $reportCard->student,
                        $reportCard->institution,
                        $downloadUrl,
                    ));
                } catch (Throwable $exception) {
                    Log::warning('Report card email failed', [
                        'report_card_id' => $reportCard->id,
                        'error' => $exception->getMessage(),
                    ]);
                }
            }

            if ($phone) {
                $sms = $smsService->send(
                    (int) preg_replace('/\D/', '', $phone),
                    $this->smsMessage($reportCard, $downloadUrl),
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

    /**
     * The text a parent gets on their phone.
     *
     * It carries the same one-time link as the email, because for a good
     * many parents the phone is the only one of the two that reaches them -
     * so this has to stand on its own rather than read as a nudge to go
     * and check an inbox.
     *
     * Written warmly but kept short: a first name rather than the full one,
     * and no praise of the results themselves, which this code has no
     * business judging. The link alone runs to about 60 characters, so
     * every word before it is one the parent pays for.
     */
    private function smsMessage(ReportCard $reportCard, string $downloadUrl): string
    {
        $firstName = strtok(trim((string) $reportCard->student->name), ' ') ?: $reportCard->student->name;
        $school = $reportCard->institution?->name;

        $opening = $school
            ? "Dear Parent, {$firstName}'s {$reportCard->term} report card from {$school} is ready."
            : "Dear Parent, {$firstName}'s {$reportCard->term} report card is ready.";

        return $opening
            .' Thank you for walking this journey with us.'
            ." Download it here (the link opens once): {$downloadUrl}";
    }
}
