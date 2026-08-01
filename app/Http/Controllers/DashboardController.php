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

        $stats = $this->analytics->forUser($user);

        return view('dashboard', compact('stats'));
    }
}
