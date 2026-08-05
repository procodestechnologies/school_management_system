<?php

namespace Modules\Classes\Http\Controllers;

use App\Http\Controllers\Concerns\Sortable;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Classes\Models\SchoolClass;
use Modules\Institution\Models\Institution;
use Modules\Student\Models\StudentDetails;

class ClassesController extends Controller
{
    use Sortable;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        abort_unless(Auth::user()->can('view classes'), 403);

        $query = SchoolClass::with(['institution', 'classTeacher']);
        $this->scopeToViewer($query);

        $classes = $this->applySort(
            $query,
            sortable: ['name', 'level', 'capacity'],
            defaultColumn: 'name',
            defaultDirection: 'asc',
        )->get();

        return view('classes::index', compact('classes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        abort_unless(Auth::user()->can('create classes'), 403);

        $institutions = isAdmin() ? Institution::all() : Auth::user()->institution;
        $teachers = $this->scopedTeachers();

        return view('classes::create', compact('institutions', 'teachers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        abort_unless(Auth::user()->can('create classes'), 403);

        $validated = $this->validated($request);

        SchoolClass::create($validated);

        return redirect()->route('classes.index')->with('success', 'Class created successfully!');
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        abort_unless(Auth::user()->can('view classes'), 403);

        $schoolClass = $this->scopedClass($id, ['institution', 'classTeacher', 'students.student']);

        $schoolClass->setRelation('students', $this->sortCollection(
            $schoolClass->students,
            sortable: [
                'name' => fn ($studentDetails) => $studentDetails->student?->name,
                'admission_number' => 'admission_number',
            ],
            defaultColumn: 'admission_number',
            defaultDirection: 'asc',
        ));

        return view('classes::show', compact('schoolClass'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        abort_unless(Auth::user()->can('edit classes'), 403);

        $schoolClass = $this->scopedClass($id);
        $institutions = isAdmin() ? Institution::all() : Auth::user()->institution;
        $teachers = $this->scopedTeachers();

        return view('classes::edit', compact('schoolClass', 'institutions', 'teachers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        abort_unless(Auth::user()->can('update classes'), 403);

        $schoolClass = $this->scopedClass($id);
        $validated = $this->validated($request);

        $schoolClass->update($validated);

        return redirect()->route('classes.show', $schoolClass->id)->with('success', 'Class updated!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        abort_unless(Auth::user()->can('delete classes'), 403);

        $schoolClass = $this->scopedClass($id);
        $schoolClass->delete();

        return redirect()->route('classes.index')->with('success', 'Class removed!');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'institution_id' => 'required|exists:institutions,id',
            'name' => 'required|string|max:255',
            'level' => 'nullable|string|max:100',
            'class_teacher_id' => 'nullable|exists:users,id',
            'capacity' => 'nullable|integer|min:1',
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

    private function scopedClass(int $id, array $with = []): SchoolClass
    {
        $query = SchoolClass::with($with);
        $this->scopeToViewer($query);

        return $query->findOrFail($id);
    }

    /**
     * Teachers selectable as a class teacher, scoped to the viewer's
     * institution(s) unless they're an Admin.
     */
    private function scopedTeachers()
    {
        $query = User::role('Teacher')->with('teacherUserDetails');

        if (! isAdmin()) {
            $institutionIds = Auth::user()->institution()->pluck('id');
            $query->whereHas('teacherUserDetails', fn ($q) => $q->whereIn('institution_id', $institutionIds));
        }

        return $query->get();
    }
}
