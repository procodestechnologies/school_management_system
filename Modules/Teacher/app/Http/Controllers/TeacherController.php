<?php

namespace Modules\Teacher\Http\Controllers;

use App\Http\Controllers\Concerns\Sortable;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Modules\Institution\Models\Institution;
use Modules\Teacher\Models\TeacherDetails;

class TeacherController extends Controller
{
    use Sortable;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        abort_unless(Auth::user()->can('view teacher'), 403);

        $query = TeacherDetails::with(['teacher', 'institution']);

        if (! isAdmin()) {
            $query->where('institution_id', currentInstitution()?->id ?? 0);
        }

        $teachers = $this->applySort(
            $query,
            sortable: ['employee_number', 'department', 'phone'],
            defaultColumn: 'created_at',
            defaultDirection: 'desc',
        )->paginate(10)->withQueryString();

        return view('teacher::index', compact('teachers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        abort_unless(Auth::user()->can('create teacher'), 403);

        $institutions = isAdmin() ? Institution::all() : collect([currentInstitution()])->filter();

        return view('teacher::create', compact('institutions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        abort_unless(Auth::user()->can('create teacher'), 403);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
            'institution_id' => ['nullable', 'exists:institutions,id'],
            'phone' => 'nullable|string|max:20',
            'employee_number' => 'nullable|string|max:100|unique:teacher_details,employee_number',
            'department' => 'nullable|string|max:255',
            'qualification' => 'nullable|string|max:255',
            'hire_date' => 'nullable|date',
            'address' => 'nullable|string',
            'status' => 'nullable|in:active,on_leave,suspended,resigned,terminated',
            'notes' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['institution_id'] = isAdmin() ? $validated['institution_id'] : currentInstitution()?->id;

        abort_unless($validated['institution_id'], 422, 'No institution selected.');

        try {
            DB::transaction(function () use ($request, $validated) {
                $teacher = User::create([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'password' => Hash::make($validated['password']),
                ]);
                $teacher->syncRoles('Teacher');

                $teacherData = collect($validated)
                    ->except(['name', 'email', 'password'])
                    ->toArray();
                $teacherData['teacher_id'] = $teacher->id;
                $teacherData['is_active'] = $request->boolean('is_active', true);

                $teacher->teacherUserDetails()->create($teacherData);
            });

            return redirect()->route('teacher.index')->with('success', 'Teacher created successfully!');
        } catch (\Exception $e) {
            Log::error('Teacher creation failed: '.$e->getMessage());

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create teacher: '.$e->getMessage());
        }
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        abort_unless(Auth::user()->can('view teacher'), 403);

        $teacherDetails = TeacherDetails::with(['teacher', 'institution'])->where('teacher_id', $id)->firstOrFail();

        $this->authorizeAccessTo($teacherDetails);

        return view('teacher::show', compact('teacherDetails'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        abort_unless(Auth::user()->can('edit teacher'), 403);

        $teacherDetails = TeacherDetails::with('teacher')->where('teacher_id', $id)->firstOrFail();

        $this->authorizeAccessTo($teacherDetails);

        $institutions = isAdmin() ? Institution::all() : collect([currentInstitution()])->filter();

        return view('teacher::edit', compact('teacherDetails', 'institutions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        abort_unless(Auth::user()->can('update teacher'), 403);

        $teacherDetails = TeacherDetails::where('teacher_id', $id)->firstOrFail();

        $this->authorizeAccessTo($teacherDetails);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,'.$id,
            'institution_id' => ['nullable', 'exists:institutions,id'],
            'phone' => 'nullable|string|max:20',
            'employee_number' => 'nullable|string|max:100|unique:teacher_details,employee_number,'.$teacherDetails->id,
            'department' => 'nullable|string|max:255',
            'qualification' => 'nullable|string|max:255',
            'hire_date' => 'nullable|date',
            'address' => 'nullable|string',
            'status' => 'nullable|in:active,on_leave,suspended,resigned,terminated',
            'notes' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['institution_id'] = isAdmin() ? $validated['institution_id'] : currentInstitution()?->id;

        abort_unless($validated['institution_id'], 422, 'No institution selected.');

        try {
            DB::transaction(function () use ($request, $validated, $teacherDetails) {
                $teacherDetails->teacher->update([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                ]);

                $teacherData = collect($validated)->except(['name', 'email'])->toArray();
                $teacherData['is_active'] = $request->boolean('is_active');

                $teacherDetails->update($teacherData);
            });

            return redirect()->route('teacher.show', $id)->with('success', 'Teacher updated successfully!');
        } catch (\Exception $e) {
            Log::error('Teacher update failed: '.$e->getMessage());

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update teacher: '.$e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        abort_unless(Auth::user()->can('delete teacher'), 403);

        $teacherDetails = TeacherDetails::where('teacher_id', $id)->firstOrFail();

        $this->authorizeAccessTo($teacherDetails);

        $teacher = User::findOrFail($id);
        $teacherDetails->delete();
        $teacher->delete();

        return redirect()->route('teacher.index')->with('success', 'Teacher removed successfully!');
    }

    /**
     * Ensure a non-admin viewer only manages teachers from their currently
     * active institution.
     */
    private function authorizeAccessTo(TeacherDetails $teacherDetails): void
    {
        if (isAdmin()) {
            return;
        }

        abort_unless($teacherDetails->institution_id === currentInstitution()?->id, 403);
    }
}
