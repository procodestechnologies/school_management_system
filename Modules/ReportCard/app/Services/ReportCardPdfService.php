<?php

namespace Modules\ReportCard\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Modules\Curriculum\Models\Curriculum;
use Modules\ReportCard\Models\GradingBand;
use Modules\ReportCard\Models\ReportCard;
use Modules\ReportCard\Models\ReportTemplate;
use Modules\ReportCard\Support\TermResolver;
use Modules\Student\Models\StudentDetails;

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

        // The mean is graded on the scale the class's curriculum runs on,
        // the same one each individual subject was marked against.
        $curriculumId = GradingBandService::curriculumIdFor($reportCard->schoolClass, $institution);
        $curriculum = $reportCard->schoolClass?->curriculum
            ?? ($curriculumId ? Curriculum::find($curriculumId) : null);

        $builder = new ReportSheetBuilder;
        $rows = $builder->rows($reportCard, $institution);
        $summary = $builder->summary($rows, $institution, $curriculumId);

        $meanPercentage = $summary['mean_percentage'];
        $meanGrade = $summary['band']?->grade;

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
        $termHistory = $this->termHistory($reportCard);

        $pdf = Pdf::loadView('reportcard::pdf.report-card', [
            'institution' => $institution,
            'student' => $student,
            'studentDetails' => $this->studentDetails($reportCard),
            'reportCard' => $reportCard,
            'rows' => $rows,
            'summary' => $summary,
            'curriculum' => $curriculum,
            'pointsCeiling' => $builder->pointsCeiling($curriculum),
            'scaleBands' => $this->scaleBands($institution, $curriculumId),
            'meanPercentage' => $meanPercentage,
            'meanGrade' => $meanGrade,
            'openingHtml' => $openingHtml,
            'closingHtml' => $closingHtml,
            'signatoryName' => $template?->signatory_name,
            'signatoryTitle' => $template?->signatory_title,
            'logoDataUri' => $logoDataUri,
            'termHistory' => $termHistory,
        ])->setPaper('a4');

        $path = "report-cards/{$reportCard->id}.pdf";
        Storage::disk('public')->put($path, $pdf->output());

        $reportCard->update(['pdf_path' => $path]);

        return $path;
    }

    /**
     * This term set against the one directly before it, for the PDF's
     * performance comparison.
     *
     * Deliberately just the one term back rather than the year to date: a
     * parent reads a report card to see whether their child moved since
     * last term, and a third column of older figures dilutes that rather
     * than sharpening it. Term 1 looks into the previous academic year, so
     * the first report of a year still has something to compare against.
     *
     * Returns the current report card alone when its term name couldn't be
     * numbered (see TermParser), since there is then no reliable way to say
     * what came before it, or when no earlier report card exists.
     */
    private function termHistory(ReportCard $reportCard): Collection
    {
        if (! $reportCard->academic_year || ! $reportCard->term_number) {
            return collect([$reportCard]);
        }

        $previousTerm = TermResolver::previous($reportCard->term_number, $reportCard->academic_year);

        $previous = ReportCard::where('student_id', $reportCard->student_id)
            ->where('academic_year', $previousTerm['academic_year'])
            ->where('term_number', $previousTerm['term_number'])
            ->first(['id', 'term', 'term_number', 'academic_year', 'mean_percentage', 'mean_grade']);

        return $previous ? collect([$previous, $reportCard]) : collect([$reportCard]);
    }

    /**
     * Admission number, gender and UPI for the header. Absent for a school
     * that hasn't filled the profile in, which the report shows as a dash
     * rather than an empty gap.
     */
    private function studentDetails(ReportCard $reportCard): ?StudentDetails
    {
        return StudentDetails::where('student_id', $reportCard->student_id)
            ->where('institution_id', $reportCard->institution_id)
            ->first();
    }

    /**
     * The scale the report was marked against, printed as a key beneath it
     * so a parent can read what EE2 or B+ actually means without being sent
     * to look it up.
     *
     * @return Collection<int, GradingBand>
     */
    private function scaleBands($institution, ?int $curriculumId): Collection
    {
        $bands = GradingBand::where('institution_id', $institution->id)
            ->when(
                $curriculumId,
                fn ($query) => $query->where('curriculum_id', $curriculumId),
                fn ($query) => $query->whereNull('curriculum_id'),
            )
            ->orderByDesc('min_percentage')
            ->get();

        // Same fallback GradingBandService grades by: a curriculum with no
        // scale of its own is marked on the school-wide one, so that is
        // the key to print.
        if ($bands->isEmpty() && $curriculumId) {
            $bands = GradingBand::where('institution_id', $institution->id)
                ->whereNull('curriculum_id')
                ->orderByDesc('min_percentage')
                ->get();
        }

        return $bands;
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
