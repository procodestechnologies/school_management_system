<?php

namespace Modules\Lesson\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Modules\Institution\Models\Institution;
use Modules\Lesson\Models\LessonReport;

class LessonReportPdfService
{
    public function __construct(private readonly LessonReportService $reportService) {}

    /**
     * Render, store, and return the PDF path for a lesson report. Recomputes
     * the day-by-day breakdown fresh (and refreshes the report's stored
     * totals) since attendance may have been corrected since it was first
     * generated.
     */
    public function generate(LessonReport $report): string
    {
        $report->loadMissing(['institution', 'schoolClass']);

        $stats = $this->reportService->compute($report->schoolClass, $report->period_start, $report->period_end);

        $report->update([
            'total_lessons' => $stats['total'],
            'attended_count' => $stats['attended'],
            'not_attended_count' => $stats['notAttended'],
            'recovered_count' => $stats['recovered'],
        ]);

        $pdf = Pdf::loadView('lesson::pdf.report', [
            'institution' => $report->institution,
            'report' => $report,
            'days' => $stats['days'],
            'logoDataUri' => $this->logoDataUri($report->institution),
        ]);

        $path = "lesson-reports/{$report->id}.pdf";
        Storage::disk('public')->put($path, $pdf->output());

        $report->update(['pdf_path' => $path, 'generated_at' => now()]);

        return $path;
    }

    private function logoDataUri(Institution $institution): ?string
    {
        if (! $institution->logo || ! Storage::disk('public')->exists($institution->logo)) {
            return null;
        }

        $mime = Storage::disk('public')->mimeType($institution->logo);
        $contents = Storage::disk('public')->get($institution->logo);

        return 'data:'.$mime.';base64,'.base64_encode($contents);
    }
}
