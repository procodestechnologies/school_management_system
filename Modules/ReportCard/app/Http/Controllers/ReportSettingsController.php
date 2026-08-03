<?php

namespace Modules\ReportCard\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Institution\Models\Institution;
use Modules\ReportCard\Models\GradingBand;
use Modules\ReportCard\Models\ReportTemplate;

class ReportSettingsController extends Controller
{
    /**
     * Show the report card settings page: grading bands + the letter
     * template used for the opening/closing text on generated PDFs.
     *
     * Directors only manage their own institution's settings; Admins pick
     * an institution first.
     */
    public function edit(Request $request)
    {
        abort_unless(Auth::user()->can('edit reportcard'), 403);

        $institution = $this->resolveInstitution($request);

        if (! $institution) {
            $institutions = isAdmin() ? Institution::all() : Auth::user()->institution;

            return view('reportcard::settings', [
                'institution' => null,
                'institutions' => $institutions,
                'gradingBands' => collect(),
                'template' => null,
            ]);
        }

        $gradingBands = GradingBand::where('institution_id', $institution->id)
            ->orderByDesc('min_percentage')
            ->get();

        $template = ReportTemplate::firstOrNew(['institution_id' => $institution->id]);

        $institutions = isAdmin() ? Institution::all() : Auth::user()->institution;

        return view('reportcard::settings', compact('institution', 'institutions', 'gradingBands', 'template'));
    }

    /**
     * Save the report template's prose fields.
     */
    public function updateTemplate(Request $request)
    {
        abort_unless(Auth::user()->can('edit reportcard'), 403);

        $institution = $this->resolveInstitution($request);
        abort_unless($institution, 404);

        $validated = $request->validate([
            'opening_text' => 'nullable|string',
            'closing_text' => 'nullable|string',
            'signatory_name' => 'nullable|string|max:255',
            'signatory_title' => 'nullable|string|max:255',
        ]);

        ReportTemplate::updateOrCreate(['institution_id' => $institution->id], $validated);

        return redirect()->route('reportcard.settings', ['institution_id' => $institution->id])
            ->with('success', 'Report template saved.');
    }

    /**
     * Add a grading band.
     */
    public function storeGradingBand(Request $request)
    {
        abort_unless(Auth::user()->can('edit reportcard'), 403);

        $institution = $this->resolveInstitution($request);
        abort_unless($institution, 404);

        $validated = $request->validate([
            'min_percentage' => 'required|numeric|min:0|max:100|lt:max_percentage',
            'max_percentage' => 'required|numeric|min:0|max:100',
            'grade' => 'required|string|max:10',
            'remark' => 'nullable|string|max:255',
        ]);

        $validated['institution_id'] = $institution->id;

        GradingBand::create($validated);

        return redirect()->route('reportcard.settings', ['institution_id' => $institution->id])
            ->with('success', 'Grading band added.');
    }

    /**
     * Remove a grading band.
     */
    public function destroyGradingBand(int $id)
    {
        abort_unless(Auth::user()->can('edit reportcard'), 403);

        $band = GradingBand::findOrFail($id);

        abort_unless(isAdmin() || Auth::user()->institution()->pluck('id')->contains($band->institution_id), 403);

        $institutionId = $band->institution_id;
        $band->delete();

        return redirect()->route('reportcard.settings', ['institution_id' => $institutionId])
            ->with('success', 'Grading band removed.');
    }

    private function resolveInstitution(Request $request): ?Institution
    {
        if (isAdmin()) {
            return $request->filled('institution_id') ? Institution::find($request->integer('institution_id')) : null;
        }

        $institutionId = $request->integer('institution_id') ?: null;
        $owned = Auth::user()->institution();

        return $institutionId
            ? $owned->where('id', $institutionId)->first()
            : $owned->first();
    }
}
