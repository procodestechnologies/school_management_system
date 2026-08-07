<?php

namespace Modules\Lesson\Http\Controllers;

use App\Http\Controllers\Concerns\Sortable;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Classes\Models\SchoolClass;
use Modules\Lesson\Models\Lesson;
use Modules\Student\Models\StudentDetails;
use Modules\Timetable\Models\TimetableEntry;

class LessonController extends Controller
{
    use Sortable;

    /**
     * Display a listing of the resource: pick a class and a date, see that
     * day's timetable periods with their attended / not attended status.
     */
    public function index(Request $request)
    {
        abort_unless(Auth::user()->can('view lesson'), 403);

        $classes = $this->scopedClasses();

        $selectedClass = null;
        $classId = $request->integer('class_id') ?: $classes->first()?->id;
        if ($classId) {
            $selectedClass = $classes->firstWhere('id', $classId);
        }

        $date = $request->filled('date') ? Carbon::parse($request->string('date')) : Carbon::today();

        $rows = collect();
        if ($selectedClass) {
            $entries = TimetableEntry::with('teacher')
                ->where('class_id', $selectedClass->id)
                ->where('day_of_week', $date->format('l'))
                ->orderBy('start_time')
                ->get();

            $lessons = Lesson::where('class_id', $selectedClass->id)
                ->whereDate('lesson_date', $date)
                ->get()
                ->keyBy('timetable_entry_id');

            $rows = $entries->map(fn (TimetableEntry $entry) => [
                'entry' => $entry,
                'lesson' => $lessons->get($entry->id),
            ]);
        }

        $recent = collect();
        if ($selectedClass) {
            $recent = $this->applySort(
                Lesson::with('timetableEntry')->where('class_id', $selectedClass->id),
                sortable: ['lesson_date', 'status'],
                defaultColumn: 'lesson_date',
                defaultDirection: 'desc',
            )->limit(10)->get();
        }

        return view('lesson::index', compact('classes', 'selectedClass', 'date', 'rows', 'recent'));
    }

    /**
     * Bulk save the attended / not attended status for every period shown
     * in the selected class's day grid.
     */
    public function store(Request $request)
    {
        abort_unless(Auth::user()->can('create lesson') || Auth::user()->can('edit lesson'), 403);

        $validated = $request->validate([
            'class_id' => 'required|exists:classes,id',
            'date' => 'required|date',
            'statuses' => 'required|array|min:1',
            'statuses.*.timetable_entry_id' => 'required|integer|exists:timetable_entries,id',
            'statuses.*.status' => 'required|in:attended,not_attended,recovered',
            'statuses.*.remarks' => 'nullable|string',
        ]);

        $class = $this->scopedClasses()->firstWhere('id', $validated['class_id']);
        abort_unless($class, 403);

        foreach ($validated['statuses'] as $row) {
            Lesson::updateOrCreate(
                [
                    'timetable_entry_id' => $row['timetable_entry_id'],
                    'lesson_date' => $validated['date'],
                ],
                [
                    'institution_id' => $class->institution_id,
                    'class_id' => $class->id,
                    'status' => $row['status'],
                    'remarks' => $row['remarks'] ?? null,
                    'marked_by' => Auth::id(),
                ]
            );
        }

        return redirect()
            ->route('lesson.index', ['class_id' => $class->id, 'date' => $validated['date']])
            ->with('success', 'Lesson attendance saved.');
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        abort_unless(Auth::user()->can('view lesson'), 403);

        $lesson = $this->scopedLesson($id, ['timetableEntry.teacher', 'schoolClass', 'markedBy']);

        return view('lesson::show', compact('lesson'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        abort_unless(Auth::user()->can('edit lesson'), 403);

        $lesson = $this->scopedLesson($id, ['timetableEntry']);

        return view('lesson::edit', compact('lesson'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        abort_unless(Auth::user()->can('edit lesson') || Auth::user()->can('update lesson'), 403);

        $lesson = $this->scopedLesson($id);

        $validated = $request->validate([
            'status' => 'required|in:attended,not_attended,recovered',
            'remarks' => 'nullable|string',
        ]);

        $lesson->update([
            'status' => $validated['status'],
            'remarks' => $validated['remarks'] ?? null,
            'marked_by' => Auth::id(),
        ]);

        return redirect()->route('lesson.show', $lesson->id)->with('success', 'Lesson updated!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        abort_unless(Auth::user()->can('delete lesson'), 403);

        $lesson = $this->scopedLesson($id);
        $classId = $lesson->class_id;
        $date = $lesson->lesson_date->format('Y-m-d');
        $lesson->delete();

        return redirect()
            ->route('lesson.index', ['class_id' => $classId, 'date' => $date])
            ->with('success', 'Lesson attendance record removed!');
    }

    private function scopeToViewer($query): void
    {
        $user = Auth::user();

        if (isAdmin()) {
            return;
        }

        if ($user->hasRole('Teacher')) {
            $institutionId = $user->teacherUserDetails?->institution_id;
            $query->where('institution_id', $institutionId ?? 0);

            return;
        }

        if ($user->hasAnyRole(['Parent', 'Student'])) {
            $institutionIds = $user->hasRole('Parent')
                ? StudentDetails::where('parent_id', $user->id)->pluck('institution_id')
                : StudentDetails::where('student_id', $user->id)->pluck('institution_id');

            $query->whereIn('institution_id', $institutionIds);

            return;
        }

        $query->where('institution_id', currentInstitution()?->id ?? 0);
    }

    private function scopedLesson(int $id, array $with = []): Lesson
    {
        $query = Lesson::with($with);
        $this->scopeToViewer($query);

        return $query->findOrFail($id);
    }

    /**
     * Classes selectable for lesson attendance, scoped to the viewer's own
     * class(es)/institution unless they're an Admin.
     */
    private function scopedClasses()
    {
        $user = Auth::user();

        if ($user->hasRole('Parent')) {
            $classIds = StudentDetails::where('parent_id', $user->id)
                ->pluck('class_id')->filter()->map(fn ($id) => (int) $id)->unique();

            return SchoolClass::whereIn('id', $classIds)->orderBy('name')->get();
        }

        if ($user->hasRole('Student')) {
            $classId = StudentDetails::where('student_id', $user->id)->value('class_id');

            return SchoolClass::whereIn('id', array_filter([(int) $classId]))->orderBy('name')->get();
        }

        if ($user->hasRole('Teacher')) {
            $institutionId = $user->teacherUserDetails?->institution_id;

            return SchoolClass::where('institution_id', $institutionId ?? 0)->orderBy('name')->get();
        }

        $query = SchoolClass::query();

        if (! isAdmin()) {
            $query->where('institution_id', currentInstitution()?->id ?? 0);
        }

        return $query->orderBy('name')->get();
    }
}
