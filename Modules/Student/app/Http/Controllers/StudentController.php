<?php

namespace Modules\Student\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Modules\Institution\Models\Institution;
use Modules\Student\Models\StudentDetails;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = StudentDetails::with(['student', 'institution']);

        if (!isAdmin()) {
            $query->whereIn('institution_id', Auth::user()->institution()->pluck('id'));
        }

        $students = $query->latest()->get();

        return view('student::index', compact('students'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $institutions = isAdmin() ? Institution::all() : Auth::user()->institution;

        return view('student::create', compact('institutions'));
    }

    /**
     * Store a newly created resource in storage.
     */
 public function store(Request $request)
{
    $validated = $request->validate([
        // Account
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255|unique:users,email',
        'password' => 'required|string|min:8',

        // Personal
        'phone' => 'nullable|string|max:20',
        'date_of_birth' => 'nullable|date',
        'gender' => 'nullable|in:male,female,other',
        'profile_photo' => 'nullable|image|max:2048',
        'admission_number' => 'nullable|string|max:100',
        'student_number' => 'nullable|string|max:100',
        'institution_id' => 'required|exists:institutions,id',
        'enrollment_status' => 'nullable|in:active,expelled,graduated,suspended,transferred,withdrawn,dropped',

        // Address
        'address' => 'nullable|string',
        'city' => 'nullable|string|max:100',
        'state' => 'nullable|string|max:100',
        'country' => 'nullable|string|max:100',

        // Parent
        'parent_name' => 'nullable|string|max:255',
        'parent_phone' => 'nullable|string|max:20',
        'parent_email' => 'nullable|email|max:255',
        'parent_occupation' => 'nullable|string|max:255',

        // Guardian
        'guardian_name' => 'nullable|string|max:255',
        'guardian_phone' => 'nullable|string|max:20',
        'guardian_email' => 'nullable|email|max:255',
        'guardian_relationship' => 'nullable|string|max:100',

        // Additional
        'medical_conditions' => 'nullable|string',
        'allergies' => 'nullable|string',
        'special_needs' => 'nullable|string',
        'notes' => 'nullable|string',
        'is_active' => 'nullable|boolean',
    ]);

    try {
        DB::transaction(function () use ($request, $validated) {
            // 1. Create Parent User (if parent details are provided)
            $parent = null;
            if (!empty($validated['parent_name']) || !empty($validated['parent_email']) || !empty($validated['parent_phone'])) {
                $parent = User::create([
                    'name' => $validated['parent_name'] ?? 'Parent',
                    'email' => $validated['parent_email'] ?? 'parent_' . time() . '@example.com',
                    'password' => Hash::make($validated['parent_phone'] ?? 'password123'),
                ]);
                $parent->syncRoles('Parent');

                // Create Parent Details
                $parentDetails = [
                    'parent_id' => $parent->id,
                    'parent_phone' => $validated['parent_phone'] ?? null,
                    'parent_occupation' => $validated['parent_occupation'] ?? null,
                ];
                $parent->parentUserDetails()->create($parentDetails);
            }

            // 2. Create Student User
            $student = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);
            $student->syncRoles('Student');

            // 3. Prepare Student Details Data
            $studentData = collect($validated)
                ->except([
                    'name', 
                    'email', 
                    'password', 
                    'is_active', 
                    'parent_name', 
                    'parent_phone', 
                    'parent_email', 
                    'parent_occupation'
                ])
                ->toArray();

            // IMPORTANT: Use 'user_id' because your relationship uses it
            $studentData['user_id'] = $student->id;
            
            // Also set student_id if you want to keep both (optional)
            $studentData['student_id'] = $student->id;
            
            // Add parent_id if parent exists
            if ($parent) {
                $studentData['parent_id'] = $parent->id;
            }
            
            $studentData['is_active'] = $request->boolean('is_active');

            // Handle profile photo upload
            if ($request->hasFile('profile_photo')) {
                $studentData['profile_photo'] = $request->file('profile_photo')
                    ->store('students/photos', 'public');
            }

            // 4. Create Student Details using the relationship
            $student->studentUserDetails()->create($studentData);
        });

        return redirect()->route('student.index')
            ->with('success', 'Student created successfully!');

    } catch (\Exception $e) {
        Log::error('Student creation failed: ' . $e->getMessage());
        Log::error('Stack trace: ' . $e->getTraceAsString());
        
        return redirect()->back()
            ->withInput()
            ->with('error', 'Failed to create student: ' . $e->getMessage());
    }
}
    /**
     * Show the specified resource.
     */
    public function show(User $student)
    {
        return view('student::show', compact('student'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('student::edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id) {}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $user = User::findOrFail($id);
        $user->studentParent->delete();
        $user->delete();
        return redirect()->route('student.index')->with('success', 'Sutudent and Parent successfully removed from institution!');
    }
}
