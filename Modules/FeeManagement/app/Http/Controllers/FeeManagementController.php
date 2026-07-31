<?php

namespace Modules\FeeManagement\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Modules\FeeManagement\Models\Fee;
use Modules\Student\Models\StudentDetails;

class FeeManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        abort_unless(Auth::user()->can('view feemanagement'), 403);

        $query = Fee::with(['student.studentUserDetails', 'institution', 'parent']);
        $this->scopeToViewer($query);

        $fees = $query->latest()->get();

        if ($request->filled('status')) {
            $fees = $fees->where('status', $request->string('status'));
        }

        return view('feemanagement::index', compact('fees'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        abort_unless(Auth::user()->can('create feemanagement'), 403);

        $students = $this->scopedStudentDetails()->with(['student', 'institution'])->get();

        return view('feemanagement::create', compact('students'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        abort_unless(Auth::user()->can('create feemanagement'), 403);

        $validated = $request->validate([
            'student_id' => 'required|exists:student_details,student_id',
            'title' => 'required|string|max:255',
            'fee_type' => 'required|string|max:100',
            'amount' => 'required|numeric|min:0',
            'amount_paid' => 'nullable|numeric|min:0',
            'due_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        // Derive institution and parent directly from the student so the fee
        // record stays consistent with StudentDetails and can't be mismatched
        // via the form.
        $studentDetails = StudentDetails::where('student_id', $validated['student_id'])->firstOrFail();

        try {
            Fee::create([
                'institution_id' => $studentDetails->institution_id,
                'student_id' => $studentDetails->student_id,
                'parent_id' => $studentDetails->parent_id,
                'title' => $validated['title'],
                'fee_type' => $validated['fee_type'],
                'amount' => $validated['amount'],
                'amount_paid' => $validated['amount_paid'] ?? 0,
                'due_date' => $validated['due_date'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]);

            return redirect()->route('feemanagement.index')
                ->with('success', 'Fee created successfully!');
        } catch (\Exception $e) {
            Log::error('Fee creation failed: ' . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create fee: ' . $e->getMessage());
        }
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        abort_unless(Auth::user()->can('view feemanagement'), 403);

        $query = Fee::with(['student.studentUserDetails', 'institution', 'parent']);
        $this->scopeToViewer($query);

        $fee = $query->findOrFail($id);

        return view('feemanagement::show', compact('fee'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        abort_unless(Auth::user()->can('edit feemanagement'), 403);

        $query = Fee::with(['student', 'institution', 'parent']);
        $this->scopeToViewer($query);

        $fee = $query->findOrFail($id);

        return view('feemanagement::edit', compact('fee'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        abort_unless(Auth::user()->can('update feemanagement'), 403);

        $query = Fee::query();
        $this->scopeToViewer($query);
        $fee = $query->findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'fee_type' => 'required|string|max:100',
            'amount' => 'required|numeric|min:0',
            'amount_paid' => 'nullable|numeric|min:0',
            'due_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        try {
            $fee->update([
                'title' => $validated['title'],
                'fee_type' => $validated['fee_type'],
                'amount' => $validated['amount'],
                'amount_paid' => $validated['amount_paid'] ?? 0,
                'due_date' => $validated['due_date'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]);

            return redirect()->route('feemanagement.show', $fee->id)
                ->with('success', 'Fee updated successfully!');
        } catch (\Exception $e) {
            Log::error('Fee update failed: ' . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update fee: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        abort_unless(Auth::user()->can('delete feemanagement'), 403);

        $query = Fee::query();
        $this->scopeToViewer($query);
        $fee = $query->findOrFail($id);

        $fee->delete();

        return redirect()->route('feemanagement.index')->with('success', 'Fee removed successfully!');
    }

    /**
     * Scope a Fee query to what the authenticated user is allowed to see:
     * admins see everything, institution owners/staff see their institutions'
     * fees, parents see their children's fees, and students see their own.
     */
    private function scopeToViewer($query): void
    {
        $user = Auth::user();

        if (isAdmin()) {
            return;
        }

        if ($user->hasRole('Parent')) {
            $query->where('parent_id', $user->id);

            return;
        }

        if ($user->hasRole('Student')) {
            $query->where('student_id', $user->id);

            return;
        }

        $query->whereIn('institution_id', $user->institution()->pluck('id'));
    }

    /**
     * Students available for fee assignment, scoped the same way as the
     * fee listing itself.
     */
    private function scopedStudentDetails()
    {
        $query = StudentDetails::query();

        if (!isAdmin()) {
            $query->whereIn('institution_id', Auth::user()->institution()->pluck('id'));
        }

        return $query;
    }
}
