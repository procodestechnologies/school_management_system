<?php

namespace Modules\Examinations\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Classes\Models\SchoolClass;
use Modules\Examinations\Models\Examination;
use Modules\Institution\Models\Institution;
use Modules\Student\Models\StudentDetails;
use Modules\Subject\Models\Subject;

class ExaminationsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        abort_unless(Auth::user()->can('view examination'), 403);

        $query = Examination::with(['institution', 'schoolClass', 'subject']);
        $this->scopeToViewer($query);

        $examinations = $query->orderBy('exam_date')->get();

        return view('examinations::index', compact('examinations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        abort_unless(Auth::user()->can('create examination'), 403);

        $institutions = isAdmin() ? Institution::all() : Auth::user()->institution;
        $classes = $this->scopedClasses();
        $subjects = $this->scopedSubjects();

        return view('examinations::create', compact('institutions', 'classes', 'subjects'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        abort_unless(Auth::user()->can('create examination'), 403);

        $validated = $this->validated($request);

        Examination::create($validated);

        return redirect()->route('examinations.index')->with('success', 'Examination created successfully!');
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        abort_unless(Auth::user()->can('view examination'), 403);

        $examination = $this->scopedExamination($id);

        return view('examinations::show', compact('examination'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        abort_unless(Auth::user()->can('edit examination'), 403);

        $examination = $this->scopedExamination($id);
        $institutions = isAdmin() ? Institution::all() : Auth::user()->institution;
        $classes = $this->scopedClasses();
        $subjects = $this->scopedSubjects();

        return view('examinations::edit', compact('examination', 'institutions', 'classes', 'subjects'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        abort_unless(Auth::user()->can('update examination'), 403);

        $examination = $this->scopedExamination($id);
        $validated = $this->validated($request);

        $examination->update($validated);

        return redirect()->route('examinations.show', $examination->id)->with('success', 'Examination updated!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        abort_unless(Auth::user()->can('delete examination'), 403);

        $examination = $this->scopedExamination($id);
        $examination->delete();

        return redirect()->route('examinations.index')->with('success', 'Examination removed!');
    }

    private function validated(Request $request): array
    {
        $validated = $request->validate([
            'institution_id' => 'required|exists:institutions,id',
            'class_id' => 'required|exists:classes,id',
            'title' => 'required|string|max:255',
            'subject_id' => 'required|exists:subjects,id',
            'term' => 'nullable|string|max:100',
            'exam_date' => 'required|date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'total_marks' => 'required|integer|min:1',
            'passing_marks' => 'nullable|integer|min:0|lte:total_marks',
            'notes' => 'nullable|string',
        ]);

        // Keep the legacy class_name/subject_name columns in sync for
        // anything still reading them directly, though class_id/subject_id
        // are now the source of truth.
        $validated['class_name'] = SchoolClass::find($validated['class_id'])?->name;
        $validated['subject_name'] = Subject::find($validated['subject_id'])?->name;

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
                ? StudentDetails::where('parent_id', $user->id)->pluck('institution_id')
                : StudentDetails::where('student_id', $user->id)->pluck('institution_id');

            $query->whereIn('institution_id', $institutionIds);

            return;
        }

        $query->whereIn('institution_id', $user->institution()->pluck('id'));
    }

    private function scopedExamination(int $id): Examination
    {
        $query = Examination::with(['institution', 'schoolClass', 'subject']);
        $this->scopeToViewer($query);

        return $query->findOrFail($id);
    }

    /**
     * Classes selectable for an examination, scoped to the viewer's
     * institution(s) unless they're an Admin.
     */
    private function scopedClasses()
    {
        $query = SchoolClass::query();

        if (! isAdmin()) {
            $query->whereIn('institution_id', Auth::user()->institution()->pluck('id'));
        }

        return $query->orderBy('name')->get();
    }

    /**
     * Subjects selectable for an examination, scoped to the viewer's
     * institution(s) unless they're an Admin.
     */
    private function scopedSubjects()
    {
        $query = Subject::query();

        if (! isAdmin()) {
            $query->whereIn('institution_id', Auth::user()->institution()->pluck('id'));
        }

        return $query->orderBy('name')->get();
    }
}
