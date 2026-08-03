<?php

namespace Modules\Result\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Classes\Models\SchoolClass;
use Modules\Examinations\Models\Examination;
use Modules\Institution\Models\Institution;
use Modules\Result\Models\Result;
use Modules\Student\Models\StudentDetails;

class ResultController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        abort_unless(Auth::user()->can('view result'), 403);

        $query = Result::with(['institution', 'schoolClass', 'student', 'examination']);
        $this->scopeToViewer($query);

        $query
            ->when($request->filled('class_id'), fn ($q) => $q->where('class_id', $request->integer('class_id')))
            ->when($request->filled('examination_id'), fn ($q) => $q->where('examination_id', $request->integer('examination_id')));

        $results = $query->latest()->get();

        $classes = $this->scopedClasses();
        $examinations = $this->scopedExaminations();

        return view('result::index', compact('results', 'classes', 'examinations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        abort_unless(Auth::user()->can('create result'), 403);

        $institutions = isAdmin() ? Institution::all() : Auth::user()->institution;
        $classes = $this->scopedClasses();
        $examinations = $this->scopedExaminations();
        $students = $this->scopedStudents();

        return view('result::create', compact('institutions', 'classes', 'examinations', 'students'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        abort_unless(Auth::user()->can('create result'), 403);

        $validated = $this->validated($request);

        if (Result::where('examination_id', $validated['examination_id'])
            ->where('student_id', $validated['student_id'])
            ->exists()) {
            return redirect()->back()->withInput()
                ->with('error', 'A result for this student in this examination already exists. Edit it instead.');
        }

        $validated['recorded_by'] = Auth::id();

        Result::create($validated);

        return redirect()->route('result.index')->with('success', 'Result recorded successfully!');
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        abort_unless(Auth::user()->can('view result'), 403);

        $result = $this->scopedResult($id);

        return view('result::show', compact('result'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        abort_unless(Auth::user()->can('edit result'), 403);

        $result = $this->scopedResult($id);
        $institutions = isAdmin() ? Institution::all() : Auth::user()->institution;
        $classes = $this->scopedClasses();
        $examinations = $this->scopedExaminations();
        $students = $this->scopedStudents();

        return view('result::edit', compact('result', 'institutions', 'classes', 'examinations', 'students'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        abort_unless(Auth::user()->can('edit result') || Auth::user()->can('update result'), 403);

        $result = $this->scopedResult($id);
        $validated = $this->validated($request);

        if (Result::where('examination_id', $validated['examination_id'])
            ->where('student_id', $validated['student_id'])
            ->where('id', '!=', $result->id)
            ->exists()) {
            return redirect()->back()->withInput()
                ->with('error', 'A result for this student in this examination already exists.');
        }

        $result->update($validated);

        return redirect()->route('result.show', $result->id)->with('success', 'Result updated!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        abort_unless(Auth::user()->can('delete result'), 403);

        $result = $this->scopedResult($id);
        $result->delete();

        return redirect()->route('result.index')->with('success', 'Result removed!');
    }

    private function validated(Request $request): array
    {
        $validated = $request->validate([
            'institution_id' => 'required|exists:institutions,id',
            'class_id' => 'required|exists:classes,id',
            'student_id' => 'required|exists:users,id',
            'examination_id' => 'required|exists:examinations,id',
            'marks_obtained' => 'required|numeric|min:0',
            'grade' => 'nullable|string|max:10',
            'remarks' => 'nullable|string',
        ]);

        $examination = Examination::find($validated['examination_id']);
        if ($examination && $validated['marks_obtained'] > $examination->total_marks) {
            abort(422, "Marks obtained can't exceed the examination's total marks ({$examination->total_marks}).");
        }

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

        if ($user->hasRole('Parent')) {
            $studentIds = StudentDetails::where('parent_id', $user->id)->pluck('student_id');
            $query->whereIn('student_id', $studentIds);

            return;
        }

        if ($user->hasRole('Student')) {
            $query->where('student_id', $user->id);

            return;
        }

        $query->whereIn('institution_id', $user->institution()->pluck('id'));
    }

    private function scopedResult(int $id): Result
    {
        $query = Result::with(['institution', 'schoolClass', 'student', 'examination', 'recordedBy']);
        $this->scopeToViewer($query);

        return $query->findOrFail($id);
    }

    /**
     * Classes selectable for a result, scoped to the viewer's
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
     * Examinations selectable for a result, scoped to the viewer's
     * institution(s) unless they're an Admin.
     */
    private function scopedExaminations()
    {
        $query = Examination::query();

        if (! isAdmin()) {
            $query->whereIn('institution_id', Auth::user()->institution()->pluck('id'));
        }

        return $query->orderByDesc('exam_date')->get();
    }

    /**
     * Students selectable for a result, scoped to the viewer's
     * institution(s) unless they're an Admin.
     */
    private function scopedStudents()
    {
        $query = StudentDetails::with('student');

        if (! isAdmin()) {
            $query->whereIn('institution_id', Auth::user()->institution()->pluck('id'));
        }

        return $query->get();
    }
}
