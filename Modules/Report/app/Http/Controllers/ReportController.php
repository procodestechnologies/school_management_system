<?php

namespace Modules\Report\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\AnalyticsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\FeeManagement\Models\Fee;

class ReportController extends Controller
{
    public function __construct(private AnalyticsService $analytics)
    {
    }

    /**
     * Display the analytics report for the authenticated user's role.
     */
    public function index()
    {
        abort_unless(Auth::user()->can('view report'), 403);

        $stats = $this->analytics->forUser(Auth::user());

        return view('report::index', compact('stats'));
    }

    /**
     * Export the viewer's fee records as CSV.
     */
    public function export(Request $request)
    {
        abort_unless(Auth::user()->can('export report'), 403);

        $user = Auth::user();

        $query = Fee::with(['student', 'institution']);

        if ($user->hasRole('Admin')) {
            // no scoping
        } elseif ($user->hasRole('Parent')) {
            $query->where('parent_id', $user->id);
        } elseif ($user->hasRole('Student')) {
            $query->where('student_id', $user->id);
        } else {
            $query->whereIn('institution_id', $user->institution()->pluck('id'));
        }

        $fees = $query->latest()->get();

        $filename = 'fees-report-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($fees) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Student', 'Institution', 'Title', 'Type', 'Amount', 'Paid', 'Balance', 'Status', 'Due Date']);

            foreach ($fees as $fee) {
                fputcsv($handle, [
                    $fee->student?->name,
                    $fee->institution?->name,
                    $fee->title,
                    $fee->fee_type,
                    $fee->amount,
                    $fee->amount_paid,
                    $fee->balance,
                    $fee->status,
                    $fee->due_date?->format('Y-m-d'),
                ]);
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }
}
