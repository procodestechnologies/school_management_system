<?php

namespace Modules\ReportCard\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Modules\ReportCard\Mail\ReportCardMail;
use Modules\ReportCard\Models\ReportCard;
use Modules\ReportCard\Services\ReportCardCompletionService;
use Modules\ReportCard\Services\ReportCardPdfService;
use Modules\Student\Models\StudentDetails;

class SendReadyReportCards extends Command
{
    protected $signature = 'reportcards:send-ready';

    protected $description = "Generate and email report cards that have been ready for at least a day, giving directors time to correct results before parents see them";

    public function handle(ReportCardCompletionService $completionService, ReportCardPdfService $pdfService): int
    {
        $reportCards = ReportCard::where('status', 'ready')
            ->where('completed_at', '<=', now()->subDay())
            ->with(['student', 'institution'])
            ->get();

        $sent = 0;
        $skipped = 0;

        foreach ($reportCards as $reportCard) {
            if (! $reportCard->student || ! $completionService->isStillComplete($reportCard->student, $reportCard->term)) {
                $reportCard->delete();
                $skipped++;

                continue;
            }

            $studentDetails = StudentDetails::where('student_id', $reportCard->student_id)->first();

            if (! $studentDetails?->parent_email) {
                $skipped++;

                continue;
            }

            $pdfService->generate($reportCard);

            $absolutePath = Storage::disk('public')->path($reportCard->pdf_path);

            Mail::to($studentDetails->parent_email)->send(new ReportCardMail(
                $reportCard,
                $reportCard->student,
                $reportCard->institution,
                $absolutePath,
            ));

            $reportCard->update(['status' => 'sent', 'sent_at' => now()]);
            $sent++;
        }

        $this->info("Sent {$sent} report card(s), skipped {$skipped}.");

        return self::SUCCESS;
    }
}
