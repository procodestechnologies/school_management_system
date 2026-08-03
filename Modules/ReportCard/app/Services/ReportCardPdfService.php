<?php

namespace Modules\ReportCard\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Modules\ReportCard\Models\ReportCard;
use Modules\ReportCard\Models\ReportTemplate;
use Modules\Result\Models\Result;

class ReportCardPdfService
{
    /**
     * Render, store, and return the PDF path for a report card. Also
     * updates the report card's mean_percentage/mean_grade with freshly
     * computed values, since marks may have been corrected since the
     * report was first marked "ready".
     */
    public function generate(ReportCard $reportCard): string
    {
        $reportCard->loadMissing(['institution', 'student', 'schoolClass']);

        $institution = $reportCard->institution;
        $student = $reportCard->student;

        $results = Result::where('student_id', $reportCard->student_id)
            ->whereHas('examination', fn ($q) => $q->where('term', $reportCard->term))
            ->with('examination.subject')
            ->get()
            ->sortBy(fn ($result) => $result->examination?->subject?->name);

        $percentages = $results
            ->filter(fn ($result) => $result->examination && $result->examination->total_marks > 0)
            ->map(fn ($result) => ($result->marks_obtained / $result->examination->total_marks) * 100);

        $meanPercentage = $percentages->isNotEmpty() ? round($percentages->avg(), 2) : null;
        $meanGrade = $meanPercentage !== null ? GradingBandService::resolve($institution, $meanPercentage) : null;

        $reportCard->update([
            'mean_percentage' => $meanPercentage,
            'mean_grade' => $meanGrade,
        ]);

        $template = ReportTemplate::where('institution_id', $institution->id)->first();

        $tokens = [
            '{{student_name}}' => $student->name,
            '{{institution_name}}' => $institution->name,
            '{{class_name}}' => $reportCard->schoolClass?->name ?? '',
            '{{term}}' => $reportCard->term,
            '{{mean_percentage}}' => $meanPercentage !== null ? number_format($meanPercentage, 2).'%' : '—',
            '{{mean_grade}}' => $meanGrade ?? '—',
        ];

        $search = array_keys($tokens);
        $replace = array_map('e', array_values($tokens));

        $defaultOpening = "Dear Parent/Guardian, please find below {$tokens['{{student_name}}']}'s report card for {$tokens['{{term}}']}.";
        $defaultClosing = 'Thank you for your continued partnership in your child\'s education.';

        $openingHtml = nl2br(str_replace($search, $replace, e($template?->opening_text ?: $defaultOpening)));
        $closingHtml = nl2br(str_replace($search, $replace, e($template?->closing_text ?: $defaultClosing)));

        $logoDataUri = $this->logoDataUri($institution);

        $pdf = Pdf::loadView('reportcard::pdf.report-card', [
            'institution' => $institution,
            'student' => $student,
            'reportCard' => $reportCard,
            'results' => $results,
            'meanPercentage' => $meanPercentage,
            'meanGrade' => $meanGrade,
            'openingHtml' => $openingHtml,
            'closingHtml' => $closingHtml,
            'signatoryName' => $template?->signatory_name,
            'signatoryTitle' => $template?->signatory_title,
            'logoDataUri' => $logoDataUri,
        ]);

        $path = "report-cards/{$reportCard->id}.pdf";
        Storage::disk('public')->put($path, $pdf->output());

        $reportCard->update(['pdf_path' => $path]);

        return $path;
    }

    private function logoDataUri($institution): ?string
    {
        if (! $institution->logo || ! Storage::disk('public')->exists($institution->logo)) {
            return null;
        }

        $mime = Storage::disk('public')->mimeType($institution->logo);
        $contents = Storage::disk('public')->get($institution->logo);

        return 'data:'.$mime.';base64,'.base64_encode($contents);
    }
}
