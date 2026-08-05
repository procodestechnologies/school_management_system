<?php

namespace Modules\Curriculum\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Curriculum\Models\Curriculum;
use Modules\Institution\Models\Institution;
use Modules\Student\Models\StudentDetails;

class CurriculumController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        abort_unless(Auth::user()->can('view curriculum'), 403);

        $query = Curriculum::with('institution');
        $this->scopeToViewer($query);

        $curricula = $query->orderBy('name')->get();

        return view('curriculum::index', compact('curricula'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        abort_unless(Auth::user()->can('create curriculum'), 403);

        $institutions = isAdmin() ? Institution::all() : Auth::user()->institution;

        return view('curriculum::create', compact('institutions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        abort_unless(Auth::user()->can('create curriculum'), 403);

        $data = $this->validated($request);

        Curriculum::create($data);

        return redirect()->route('curriculum.index')->with('success', 'Curriculum created successfully!');
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        abort_unless(Auth::user()->can('view curriculum'), 403);

        $curriculum = $this->scopedCurriculum($id);
        $curriculum->load('institution');

        return view('curriculum::show', compact('curriculum'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        abort_unless(Auth::user()->can('edit curriculum'), 403);

        $curriculum = $this->scopedCurriculum($id);
        $institutions = isAdmin() ? Institution::all() : Auth::user()->institution;

        return view('curriculum::edit', compact('curriculum', 'institutions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        abort_unless(Auth::user()->can('update curriculum'), 403);

        $curriculum = $this->scopedCurriculum($id);
        $data = $this->validated($request);

        $curriculum->update($data);

        return redirect()->route('curriculum.index')->with('success', 'Curriculum updated!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        abort_unless(Auth::user()->can('delete curriculum'), 403);

        $curriculum = $this->scopedCurriculum($id);
        $curriculum->delete();

        return back()->with('success', 'Curriculum deleted successfully!');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'institution_id' => 'required|exists:institutions,id',
            'name' => 'required|string|max:255',
            'status' => 'required|in:active,dismissed',
        ]);
    }

    private function scopeToViewer($query): void
    {
        $user = Auth::user();

        if (isAdmin()) {
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

    private function scopedCurriculum(int $id): Curriculum
    {
        $query = Curriculum::with('institution');
        $this->scopeToViewer($query);

        return $query->findOrFail($id);
    }
}
