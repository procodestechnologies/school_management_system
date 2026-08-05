<?php

namespace Modules\ReportCard\Http\Controllers;

use App\Http\Controllers\Concerns\Sortable;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\ReportCard\Models\ReportCard;
use Modules\Student\Models\StudentDetails;

class ReportCardController extends Controller
{
    use Sortable;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        abort_unless(Auth::user()->can('view reportcard'), 403);

        $query = ReportCard::with(['institution', 'schoolClass', 'student']);
        $this->scopeToViewer($query);

        $reportCards = $this->applySort(
            $query,
            sortable: ['term', 'mean_grade'],
            defaultColumn: 'completed_at',
            defaultDirection: 'desc',
        )->get();

        return view('reportcard::index', compact('reportCards'));
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        abort_unless(Auth::user()->can('view reportcard'), 403);

        $query = ReportCard::with(['institution', 'schoolClass', 'student']);
        $this->scopeToViewer($query);

        $reportCard = $query->findOrFail($id);

        return view('reportcard::show', compact('reportCard'));
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

        if ($user->hasRole('Parent')) {
            $studentIds = StudentDetails::where('parent_id', $user->id)->pluck('student_id');
            $query->whereIn('student_id', $studentIds);

            return;
        }

        if ($user->hasRole('Student')) {
            $query->where('student_id', $user->id);

            return;
        }

        $query->whereIn('institution_id', $user->institution()->pluck('id'));
    }
}
