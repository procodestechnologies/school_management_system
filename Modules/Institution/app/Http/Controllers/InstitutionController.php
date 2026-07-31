<?php

namespace Modules\Institution\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Flux\Flux;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Curriculum\Models\Curriculum;
use Modules\Institution\Models\Institution;

class InstitutionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public User $user;
    public array $institution;
    public function __construct()
    {
        $this->user = Auth::user();
    }
    public function index()
    {
        abort_unless($this->user->can('view institution'), 403);

        if (isAdmin()) {
            $institution = Institution::with('owner')->get();
            return view('institution::index', compact('institution'));
        }

        $institution = $this->user->institution;
        return view('institution::index', compact('institution'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * Institution creation is the self-service "onboard my school" flow: any
     * authenticated user without an institution yet may reach it (this is
     * how a Director account comes into being). Once a user owns a school
     * they can no longer create another one unless they're an Admin, who
     * owns the platform and may onboard schools on a client's behalf.
     */
    public function create()
    {
        abort_if(!isAdmin() && $this->user->institution()->exists(), 403);

        $curricula = Curriculum::all();
        return view('institution::create', compact('curricula'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        abort_if(!isAdmin() && $this->user->institution()->exists(), 403);

        $data = $request->validate([
            "name" => "required|string",
            "curriculum" => "required|int",
            "code" => "required",
            "type" => "required",
            "email" => "required",
            "phone" => "required",
            "alternate_phone" => "nullable",
            "website" => "nullable",
            "country" => "nullable",
            "county" => "required",
            "city" => "required",
            "postal_address" => "required",
            "physical_address" => "required",
        ]);
        $user = $request->user();
        $user->institution()->create($data);

        // Owning a school makes this user its Director, unless they already
        // hold a higher-privilege role (e.g. Admin creating it on their
        // behalf).
        if (!$user->hasAnyRole(['Admin', 'Director'])) {
            $user->assignRole('Director');
        }

        return redirect()->route('institution.index')->with('success', 'Institution created successfully!');
    }

    /**
     * Show the specified resource.
     */
    public function show(Institution $institution)
    {
        abort_unless(
            $this->user->can('view institution') && (isAdmin() || $institution->user_id === $this->user->id),
            403
        );

        return view('institution::show', compact('institution'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id)
    {
        $institution = Institution::whereId($id)->first();

        abort_unless(
            $this->user->can('edit institution') && (isAdmin() || $institution?->user_id === $this->user->id),
            403
        );

        $curricula = Curriculum::all();
        return view('institution::edit', compact('curricula', 'institution'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $existing = Institution::find($id);

        abort_unless(
            $this->user->can('update institution') && (isAdmin() || $existing?->user_id === $this->user->id),
            403
        );

        // Validate the request
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:institutions,code,' . $id,
            'type' => 'required|string|in:School,College,University,Training Centre',
            'curriculum' => 'required|exists:curricula,id',
            'education_level' => 'nullable|string|max:100',
            'timezone' => 'required|string|max:50',

            // Contact Information
            'email' => 'required|email|max:255|unique:institutions,email,' . $id,
            'phone' => 'required|string|max:20',
            'alternate_phone' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',

            // Address
            'country' => 'nullable|string|max:100',
            'county' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'postal_address' => 'nullable|string|max:255',
            'physical_address' => 'nullable|string',

            // Additional Information
            'principal_name' => 'nullable|string|max:255',
            'principal_phone' => 'nullable|string|max:20',
            'subscription_plan' => 'nullable|string',
            'subscription_expires_at' => 'nullable|date|after:today',
            'notes' => 'nullable|string',

            // Status
            'is_active' => 'required|boolean',
        ]);

        try {
            // Find the institution
            $institution = Institution::findOrFail($id);

            // Format subscription expiry date if provided
            if ($request->filled('subscription_expires_at')) {
                $validated['subscription_expires_at'] = Carbon::parse($request->subscription_expires_at)->format('Y-m-d H:i:s');
            }

            // Update the institution
            $institution->update($validated);

            // Optional: Log the update activity
            // ActivityLog::create([
            //     'user_id' => auth()->id(),
            //     'action' => 'updated',
            //     'model' => 'Institution',
            //     'model_id' => $institution->id,
            //     'changes' => $institution->getChanges()
            // ]);

            // Flash success message
            session()->flash('update.inst', 'Institution updated successfully!');

            // Redirect to show page with success message
            return redirect()->route('institution.edit', $institution->id)
                ->with('success', 'Institution "' . $institution->name . '" has been updated successfully.');
        } catch (ModelNotFoundException $e) {
            // Institution not found
            return redirect()->route('institution.index')
                ->with('error', 'Institution not found.');
        } catch (QueryException $e) {
            // Database error
            return redirect()->back()
                ->withInput()
                ->with('error', 'Database error: ' . $e->getMessage());
        } catch (\Exception $e) {
            // General error
            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred while updating the institution. Please try again.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Institution $institution)
    {
        // Only the platform owner may de-register a school.
        abort_unless(isAdmin() && $this->user->can('delete institution'), 403);

        $institution->delete();
        return redirect()->route('institution.index');
    }
}
