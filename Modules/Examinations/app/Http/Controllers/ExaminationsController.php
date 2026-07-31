<?php

namespace Modules\Examinations\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Examinations\Models\Examination;
use Modules\Institution\Models\Institution;
use Modules\Student\Models\StudentDetails;

class ExaminationsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        abort_unless(Auth::user()->can('view examination'), 403);

        $query = Examination::with('institution');
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

        return view('examinations::create', compact('institutions'));
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

        return view('examinations::edit', compact('examination', 'institutions'));
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
        return $request->validate([
            'institution_id' => 'required|exists:institutions,id',
            'title' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'class_name' => 'required|string|max:255',
            'term' => 'nullable|string|max:100',
            'exam_date' => 'required|date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'total_marks' => 'required|integer|min:1',
            'passing_marks' => 'nullable|integer|min:0|lte:total_marks',
            'notes' => 'nullable|string',
        ]);
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
        $query = Examination::with('institution');
        $this->scopeToViewer($query);

        return $query->findOrFail($id);
    }
}
