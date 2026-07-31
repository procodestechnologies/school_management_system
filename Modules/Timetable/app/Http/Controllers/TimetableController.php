<?php

namespace Modules\Timetable\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Classes\Models\SchoolClass;
use Modules\Institution\Models\Institution;
use Modules\Timetable\Models\TimetableEntry;
use Modules\Timetable\Services\TimetableImportService;

class TimetableController extends Controller
{
    private const DAYS = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

    /**
     * Display a listing of the resource: pick a class, see its timetable as
     * a day-by-period grid.
     */
    public function index(Request $request)
    {
        abort_unless(Auth::user()->can('view timetable'), 403);

        $classes = $this->scopedClasses();

        $selectedClass = null;
        $grid = null;
        $periods = collect();
        $days = self::DAYS;

        $classId = $request->integer('class_id') ?: $classes->first()?->id;

        if ($classId) {
            $selectedClass = $classes->firstWhere('id', $classId);
        }

        if ($selectedClass) {
            $entries = TimetableEntry::with(['teacher'])
                ->where('class_id', $selectedClass->id)
                ->get();

            $periods = $entries
                ->map(fn (TimetableEntry $e) => $e->start_time?->format('H:i') . '-' . $e->end_time?->format('H:i'))
                ->unique()
                ->sort()
                ->values();

            $grid = [];
            foreach ($entries as $entry) {
                $periodKey = $entry->start_time?->format('H:i') . '-' . $entry->end_time?->format('H:i');
                $grid[$entry->day_of_week][$periodKey] = $entry;
            }
        }

        return view('timetable::index', compact('classes', 'selectedClass', 'grid', 'periods', 'days'));
    }

    /**
     * Show the form for importing a class's timetable from a CSV/XLS file.
     */
    public function import()
    {
        abort_unless(Auth::user()->can('create timetable'), 403);

        $classes = $this->scopedClasses();

        return view('timetable::import', compact('classes'));
    }

    /**
     * Handle the uploaded file and merge it into the selected class's
     * timetable.
     */
    public function importStore(Request $request, TimetableImportService $importer)
    {
        abort_unless(Auth::user()->can('create timetable'), 403);

        $validated = $request->validate([
            'class_id' => 'required|exists:classes,id',
            'file' => 'required|file|mimes:csv,txt,xls,xlsx|max:5120',
        ]);

        $class = $this->scopedClasses()->firstWhere('id', $validated['class_id']);
        abort_unless($class, 403);

        $result = $importer->import($request->file('file'), $class);

        if ($result['created'] === 0) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Import failed: ' . implode(' ', $result['errors']));
        }

        $message = "Imported {$result['created']} timetable entries for {$class->name}.";
        if ($result['skipped'] > 0) {
            $message .= " Skipped {$result['skipped']} break/lunch row(s).";
        }
        if (!empty($result['errors'])) {
            $message .= ' ' . count($result['errors']) . ' row(s) had errors: ' . implode(' ', $result['errors']);
        }
        if (!empty($result['warnings'])) {
            $message .= ' ' . implode(' ', $result['warnings']);
        }

        return redirect()->route('timetable.index', ['class_id' => $class->id])->with('success', $message);
    }

    /**
     * Download a blank CSV template for the import.
     */
    public function importTemplate()
    {
        abort_unless(Auth::user()->can('create timetable'), 403);

        $rows = [
            ['Day', 'Start Time', 'End Time', 'Subject', 'Teacher Email', 'Room'],
            ['Monday', '08:20', '08:55', 'MATHS', '', ''],
            ['Monday', '08:55', '09:30', 'PHE', '', ''],
            ['Monday', '09:50', '10:25', 'ENG', '', ''],
        ];

        return response()->streamDownload(function () use ($rows) {
            $handle = fopen('php://output', 'w');
            foreach ($rows as $row) {
                fputcsv($handle, $row);
            }
            fclose($handle);
        }, 'timetable-import-template.csv', ['Content-Type' => 'text/csv']);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        abort_unless(Auth::user()->can('create timetable'), 403);

        $institutions = isAdmin() ? Institution::all() : Auth::user()->institution;
        $teachers = $this->scopedTeachers();
        $classes = $this->scopedClasses();
        $days = self::DAYS;

        return view('timetable::create', compact('institutions', 'teachers', 'classes', 'days'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        abort_unless(Auth::user()->can('create timetable'), 403);

        $validated = $this->validated($request);

        TimetableEntry::create($validated);

        return redirect()->route('timetable.index')->with('success', 'Timetable entry created successfully!');
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        abort_unless(Auth::user()->can('view timetable'), 403);

        $entry = $this->scopedEntry($id, ['institution', 'teacher', 'schoolClass']);

        return view('timetable::show', compact('entry'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        abort_unless(Auth::user()->can('edit timetable'), 403);

        $entry = $this->scopedEntry($id);
        $institutions = isAdmin() ? Institution::all() : Auth::user()->institution;
        $teachers = $this->scopedTeachers();
        $classes = $this->scopedClasses();
        $days = self::DAYS;

        return view('timetable::edit', compact('entry', 'institutions', 'teachers', 'classes', 'days'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        abort_unless(Auth::user()->can('update timetable'), 403);

        $entry = $this->scopedEntry($id);
        $validated = $this->validated($request);

        $entry->update($validated);

        return redirect()->route('timetable.show', $entry->id)->with('success', 'Timetable entry updated!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        abort_unless(Auth::user()->can('delete timetable'), 403);

        $entry = $this->scopedEntry($id);
        $entry->delete();

        return redirect()->route('timetable.index')->with('success', 'Timetable entry removed!');
    }

    private function validated(Request $request): array
    {
        $validated = $request->validate([
            'institution_id' => 'required|exists:institutions,id',
            'class_id' => 'required|exists:classes,id',
            'teacher_id' => 'nullable|exists:users,id',
            'subject' => 'required|string|max:255',
            'day_of_week' => 'required|in:' . implode(',', self::DAYS),
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'room' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        // Keep the legacy class_name column in sync for anything still
        // reading it directly, though class_id is now the source of truth.
        $validated['class_name'] = SchoolClass::find($validated['class_id'])?->name;

        return $validated;
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
                ? \Modules\Student\Models\StudentDetails::where('parent_id', $user->id)->pluck('institution_id')
                : \Modules\Student\Models\StudentDetails::where('student_id', $user->id)->pluck('institution_id');

            $query->whereIn('institution_id', $institutionIds);

            return;
        }

        $query->whereIn('institution_id', $user->institution()->pluck('id'));
    }

    private function scopedEntry(int $id, array $with = []): TimetableEntry
    {
        $query = TimetableEntry::with($with);
        $this->scopeToViewer($query);

        return $query->findOrFail($id);
    }

    /**
     * Teachers selectable for a timetable entry, scoped to the viewer's
     * institution(s) unless they're an Admin.
     */
    private function scopedTeachers()
    {
        $query = User::role('Teacher')->with('teacherUserDetails');

        if (!isAdmin()) {
            $institutionIds = Auth::user()->institution()->pluck('id');
            $query->whereHas('teacherUserDetails', fn ($q) => $q->whereIn('institution_id', $institutionIds));
        }

        return $query->get();
    }

    /**
     * Classes selectable for a timetable entry, scoped to the viewer's
     * institution(s) unless they're an Admin.
     */
    private function scopedClasses()
    {
        $query = SchoolClass::query();

        if (!isAdmin()) {
            $query->whereIn('institution_id', Auth::user()->institution()->pluck('id'));
        }

        return $query->orderBy('name')->get();
    }
}
