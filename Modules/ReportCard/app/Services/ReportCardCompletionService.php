<?php

namespace Modules\ReportCard\Services;

use App\Models\User;
use Modules\ReportCard\Models\ReportCard;
use Modules\Result\Models\Result;
use Modules\Selections\Models\SubjectSelection;
use Modules\Student\Models\StudentDetails;
use Modules\Subject\Models\Subject;

class ReportCardCompletionService
{
    /**
     * Check whether a student now has a Result for every required subject
     * (compulsory + their own electives) within a given exam term, and if
     * so, mark their report card as "ready" to be sent (the actual send
     * happens later, on a delay - see the SendReadyReportCards command).
     */
    public function handle(User $student, string $term): void
    {
        $studentDetails = StudentDetails::where('student_id', $student->id)->first();

        if (! $studentDetails) {
            return;
        }

        $requiredSubjectIds = $this->requiredSubjectIds($student, $studentDetails->institution_id);

        // Never mark a report "ready" against an empty requirement set -
        // e.g. no compulsory subjects configured and no electives picked.
        if ($requiredSubjectIds->isEmpty()) {
            return;
        }

        $coveredSubjectIds = Result::where('student_id', $student->id)
            ->whereHas('examination', fn ($q) => $q->where('term', $term)->whereIn('subject_id', $requiredSubjectIds))
            ->with('examination:id,subject_id')
            ->get()
            ->pluck('examination.subject_id')
            ->unique();

        if ($requiredSubjectIds->diff($coveredSubjectIds)->isNotEmpty()) {
            return;
        }

        ReportCard::firstOrCreate(
            ['student_id' => $student->id, 'term' => $term],
            [
                'institution_id' => $studentDetails->institution_id,
                'class_id' => (int) $studentDetails->class_id,
                'status' => 'ready',
                'completed_at' => now(),
            ]
        );
    }

    /**
     * Whether the student still has a complete set of results for this
     * term - used to re-verify freshness right before actually sending,
     * since results may have been corrected/removed during the send delay.
     */
    public function isStillComplete(User $student, string $term): bool
    {
        $studentDetails = StudentDetails::where('student_id', $student->id)->first();

        if (! $studentDetails) {
            return false;
        }

        $requiredSubjectIds = $this->requiredSubjectIds($student, $studentDetails->institution_id);

        if ($requiredSubjectIds->isEmpty()) {
            return false;
        }

        $coveredSubjectIds = Result::where('student_id', $student->id)
            ->whereHas('examination', fn ($q) => $q->where('term', $term)->whereIn('subject_id', $requiredSubjectIds))
            ->with('examination:id,subject_id')
            ->get()
            ->pluck('examination.subject_id')
            ->unique();

        return $requiredSubjectIds->diff($coveredSubjectIds)->isEmpty();
    }

    private function requiredSubjectIds(User $student, int $institutionId)
    {
        $compulsoryIds = Subject::where('institution_id', $institutionId)
            ->where('is_compulsory', true)
            ->where('is_active', true)
            ->pluck('id');

        $electiveIds = Subject::whereIn('id',
            SubjectSelection::where('student_id', $student->id)->pluck('subject_id')
        )->where('is_active', true)->pluck('id');

        return $compulsoryIds->merge($electiveIds)->unique();
    }
}
