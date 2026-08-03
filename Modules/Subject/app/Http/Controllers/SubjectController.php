<?php

namespace Modules\Subject\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Institution\Models\Institution;
use Modules\Student\Models\StudentDetails;
use Modules\Subject\Models\Subject;

class SubjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        abort_unless(Auth::user()->can('view subject'), 403);

        $query = Subject::with('institution');
        $this->scopeToViewer($query);

        $subjects = $query->orderBy('name')->get();

        return view('subject::index', compact('subjects'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        abort_unless(Auth::user()->can('create subject'), 403);

        $institutions = isAdmin() ? Institution::all() : Auth::user()->institution;

        return view('subject::create', compact('institutions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        abort_unless(Auth::user()->can('create subject'), 403);

        $validated = $this->validated($request);

        Subject::create($validated);

        return redirect()->route('subject.index')->with('success', 'Subject created successfully!');
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        abort_unless(Auth::user()->can('view subject'), 403);

        $subject = $this->scopedSubject($id, ['examinations']);

        return view('subject::show', compact('subject'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        abort_unless(Auth::user()->can('edit subject'), 403);

        $subject = $this->scopedSubject($id);
        $institutions = isAdmin() ? Institution::all() : Auth::user()->institution;

        return view('subject::edit', compact('subject', 'institutions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        abort_unless(Auth::user()->can('edit subject') || Auth::user()->can('update subject'), 403);

        $subject = $this->scopedSubject($id);
        $validated = $this->validated($request);

        $subject->update($validated);

        return redirect()->route('subject.show', $subject->id)->with('success', 'Subject updated!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        abort_unless(Auth::user()->can('delete subject'), 403);

        $subject = $this->scopedSubject($id);
        $subject->delete();

        return redirect()->route('subject.index')->with('success', 'Subject removed!');
    }

    private function validated(Request $request): array
    {
        $validated = $request->validate([
            'institution_id' => 'required|exists:institutions,id',
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
        ]);

        $validated['is_compulsory'] = $request->boolean('is_compulsory');
        $validated['is_active'] = $request->boolean('is_active', true);

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

    private function scopedSubject(int $id, array $with = []): Subject
    {
        $query = Subject::with(array_merge(['institution'], $with));
        $this->scopeToViewer($query);

        return $query->findOrFail($id);
    }
}
