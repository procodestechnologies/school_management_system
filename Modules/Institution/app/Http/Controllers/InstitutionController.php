<?php

namespace Modules\Institution\Http\Controllers;

use App\Http\Controllers\Concerns\Sortable;
use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\User;
use App\Services\ImageCompressionService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Modules\Institution\Models\Institution;

class InstitutionController extends Controller
{
    use Sortable;

    /**
     * Display a listing of the resource.
     */
    public User $user;

    public array $institution;

    public function __construct(
        private readonly ImageCompressionService $imageCompressor,
    ) {
        $this->user = Auth::user();
    }

    public function index()
    {
        abort_unless($this->user->can('view institution'), 403);

        if (isAdmin()) {
            $institution = $this->applySort(
                Institution::with('owner'),
                sortable: ['name', 'code', 'phone', 'email', 'created_at'],
                defaultColumn: 'created_at',
                defaultDirection: 'desc',
            )->get();

            return view('institution::index', compact('institution'));
        }

        $institution = $this->user->institution;

        return view('institution::index', compact('institution'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * Institution creation is the self-service "onboard my school" flow: any
     * authenticated user may reach it (this is how a Director account comes
     * into being) - and a Director may own several schools, so having one
     * already is no longer a block. Admins own the platform, not a school,
     * and may never create one - their role in this flow is to approve a
     * self-created institution afterward, not to create it.
     */
    public function create()
    {
        abort_if(isAdmin(), 403);

        return view('institution::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        abort_if(isAdmin(), 403);

        $data = $request->validate([
            'name' => 'required|string',
            'code' => 'required',
            'type' => 'required',
            'email' => 'required',
            'phone' => 'required',
            'alternate_phone' => 'nullable',
            'website' => 'nullable',
            'country' => 'nullable',
            'county' => 'required',
            'city' => 'required',
            'postal_address' => 'required',
            'physical_address' => 'required',
            'logo' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            $data['logo'] = $this->imageCompressor->store($request->file('logo'), 'institutions/logos');
        }

        // New institutions start unapproved regardless of the column's
        // default (which only exists to grandfather in institutions that
        // existed before the approval workflow was introduced).
        $data['is_approved'] = false;

        $user = $request->user();
        $user->institution()->create($data);

        // Owning a school makes this user its Director.
        if (! $user->hasRole('Director')) {
            $user->assignRole('Director');
        }

        return redirect()->route('institution.index')->with('success', 'Institution created successfully! It will be reviewed by an Admin before it becomes fully active.');
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

        $plans = Plan::where('is_active', true)->orderBy('name')->get();

        return view('institution::edit', compact('institution', 'plans'));
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
            'education_level' => 'nullable|string|max:100',
            'timezone' => 'required|string|max:50',
            'logo' => 'nullable|image|max:2048',
            'min_electives' => 'required|integer|min:0',
            'max_electives' => 'nullable|integer|gte:min_electives',

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
            'subscription_plan' => 'nullable|exists:plans,id',
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

            // Handle logo upload
            if ($request->hasFile('logo')) {
                $uploadedLogo = $request->file('logo');

                Log::debug('Institution logo upload received', [
                    'institution_id' => $institution->id,
                    'client_name' => $uploadedLogo->getClientOriginalName(),
                    'client_mime' => $uploadedLogo->getClientMimeType(),
                    'size' => $uploadedLogo->getSize(),
                    'is_valid' => $uploadedLogo->isValid(),
                    'upload_error_code' => $uploadedLogo->getError(),
                ]);

                if ($institution->logo && Storage::disk('public')->exists($institution->logo)) {
                    Storage::disk('public')->delete($institution->logo);
                }

                $validated['logo'] = $this->imageCompressor->store($uploadedLogo, 'institutions/logos');

                Log::debug('Institution logo stored locally', [
                    'institution_id' => $institution->id,
                    'stored_path' => $validated['logo'],
                ]);
            }

            // Update the institution
            $institution->update($validated);

            Log::debug('Institution update persisted', [
                'institution_id' => $institution->id,
                'logo_after_save' => $institution->fresh()->logo,
                'logo_key_was_present_in_validated' => array_key_exists('logo', $validated),
            ]);

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
            Log::error('Institution update failed with a database error', [
                'institution_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Database error: ' . $e->getMessage());
        } catch (\Throwable $e) {
            // General error - \Throwable (not just \Exception) so a logo
            // upload failure of any kind is surfaced instead of silently
            // producing a save with no logo and no visible error.
            Log::error('Institution update failed', [
                'institution_id' => $id,
                'error' => $e->getMessage(),
                'class' => get_class($e),
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred while updating the institution: ' . $e->getMessage());
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

    /**
     * Approve a self-created institution, unlocking full access for its
     * Director. Admins review and approve; they never create institutions
     * themselves.
     */
    public function approve(Institution $institution)
    {
        abort_unless(isAdmin(), 403);

        $institution->update([
            'is_approved' => true,
            'approved_at' => now(),
            'approved_by_id' => $this->user->id,
        ]);

        return back()->with('success', 'Institution "' . $institution->name . '" has been approved.');
    }

    /**
     * Set which of a Director's institutions the rest of the system
     * (dashboard, billing, modules) runs as for them. Remembered across
     * logins until they choose a different one.
     */
    public function choose(Institution $institution)
    {
        abort_unless($institution->user_id === $this->user->id, 403);

        $this->user->update(['active_institution_id' => $institution->id]);

        return redirect()->route('dashboard')
            ->with('success', 'Now managing "' . $institution->name . '".');
    }
}
