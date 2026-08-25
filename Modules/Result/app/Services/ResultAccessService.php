<?php

namespace Modules\Result\Services;

use App\Models\User;
use Illuminate\Support\Collection;
use Modules\Classes\Models\SchoolClass;
use Modules\Subject\Models\SubjectTeacher;
use Modules\Timetable\Models\TimetableEntry;

/**
 * Which results a teacher may see and enter.
 *
 * Two doors lead in, and a teacher only needs one of them:
 *
 * - Subject teacher: they've been assigned a subject in a class (or are
 *   timetabled to teach it), and own that subject's marks for that class.
 * - Class teacher: the class is theirs, so every subject in it is theirs to
 *   enter - they're the one collating the class's report cards.
 *
 * Timetable entries still count as an assignment. They were the only signal
 * before subject_teachers existed, and a school that has scheduled a
 * teacher for a subject has said the same thing a different way.
 */
class ResultAccessService
{
    /**
     * The (class_id, subject_id) pairs a teacher owns marks for, from
     * explicit assignments and from the timetable alike.
     *
     * @return Collection<int, object{class_id: int, subject_id: int}>
     */
    public static function subjectPairs(User $teacher): Collection
    {
        $assigned = SubjectTeacher::where('teacher_id', $teacher->id)
            ->get(['class_id', 'subject_id']);

        $timetabled = TimetableEntry::where('teacher_id', $teacher->id)
            ->whereNotNull('subject_id')
            ->whereNotNull('class_id')
            ->get(['class_id', 'subject_id']);

        return $assigned->concat($timetabled)
            ->map(fn ($entry) => (object) ['class_id' => (int) $entry->class_id, 'subject_id' => (int) $entry->subject_id])
            ->unique(fn ($pair) => $pair->class_id.'-'.$pair->subject_id)
            ->values();
    }

    /**
     * Classes this teacher is the class teacher of - every subject in them
     * is theirs to enter.
     *
     * @return Collection<int, int>
     */
    public static function classTeacherClassIds(User $teacher): Collection
    {
        return SchoolClass::where('class_teacher_id', $teacher->id)
            ->pluck('id')
            ->map(fn ($id) => (int) $id);
    }

    /**
     * Every class the teacher may enter results in, by either door.
     *
     * @return Collection<int, int>
     */
    public static function classIds(User $teacher): Collection
    {
        return self::subjectPairs($teacher)
            ->pluck('class_id')
            ->concat(self::classTeacherClassIds($teacher))
            ->unique()
            ->values();
    }

    /**
     * Whether this teacher may enter or amend a mark for the given class
     * and subject. A null subject only passes for a class teacher - there's
     * no subject to have been assigned.
     */
    public static function canGrade(User $teacher, int $classId, ?int $subjectId): bool
    {
        if (self::classTeacherClassIds($teacher)->contains($classId)) {
            return true;
        }

        if ($subjectId === null) {
            return false;
        }

        return self::subjectPairs($teacher)
            ->contains(fn ($pair) => $pair->class_id === $classId && $pair->subject_id === $subjectId);
    }

    /**
     * Narrow a Result query to what this teacher may see: their own
     * subjects in the classes they teach, plus everything in the classes
     * they're class teacher of.
     */
    public static function scopeResults($query, User $teacher): void
    {
        $pairs = self::subjectPairs($teacher);
        $classTeacherClassIds = self::classTeacherClassIds($teacher);

        $query->where(function ($q) use ($pairs, $classTeacherClassIds) {
            foreach ($pairs as $pair) {
                $q->orWhere(function ($q2) use ($pair) {
                    $q2->where('class_id', $pair->class_id)
                        ->whereHas('examination', fn ($q3) => $q3->where('subject_id', $pair->subject_id));
                });
            }

            if ($classTeacherClassIds->isNotEmpty()) {
                $q->orWhereIn('class_id', $classTeacherClassIds);
            }

            // Nothing assigned and no class of their own - the teacher sees
            // nothing rather than everything at the institution.
            if ($pairs->isEmpty() && $classTeacherClassIds->isEmpty()) {
                $q->whereRaw('1 = 0');
            }
        });
    }

    /**
     * The same narrowing, for a query over Examinations rather than
     * Results - examinations carry the subject on themselves.
     */
    public static function scopeExaminations($query, User $teacher): void
    {
        $pairs = self::subjectPairs($teacher);
        $classTeacherClassIds = self::classTeacherClassIds($teacher);

        $query->where(function ($q) use ($pairs, $classTeacherClassIds) {
            foreach ($pairs as $pair) {
                $q->orWhere(fn ($q2) => $q2->where('class_id', $pair->class_id)->where('subject_id', $pair->subject_id));
            }

            if ($classTeacherClassIds->isNotEmpty()) {
                $q->orWhereIn('class_id', $classTeacherClassIds);
            }

            if ($pairs->isEmpty() && $classTeacherClassIds->isEmpty()) {
                $q->whereRaw('1 = 0');
            }
        });
    }
}
