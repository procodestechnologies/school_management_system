<?php

namespace Modules\Timetable\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Classes\Models\SchoolClass;
use Modules\Institution\Models\Institution;
use Modules\Timetable\Models\TimetableEntry;

class TimetableController extends Controller
{
    private const DAYS = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        abort_unless(Auth::user()->can('view timetable'), 403);

        $query = TimetableEntry::with(['institution', 'teacher', 'schoolClass']);
        $this->scopeToViewer($query);

        $dayOrder = array_flip(self::DAYS);

        $entries = $query->orderBy('start_time')
            ->get()
            ->sortBy(fn (TimetableEntry $entry) => $dayOrder[$entry->day_of_week] ?? 99)
            ->values();

        return view('timetable::index', compact('entries'));
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
