<?php

namespace App\Http\Controllers;

use App\Services\AnalyticsService;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct(private AnalyticsService $analytics) {}

    public function __invoke()
    {
        $user = Auth::user();

        abort_unless($user->can('view dashboard'), 403);

        // The Admin's "dashboard" is the full analytics view - there's no
        // separate, lighter landing page for them to see first.
        if ($user->hasRole('Admin')) {
            return redirect()->route('report.index');
        }

        $stats = $this->analytics->forUser($user);

        return view('dashboard', compact('stats'));
    }
}
