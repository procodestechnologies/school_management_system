<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class HasInstitution
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (! $user) {
            return redirect()->route('login');
        }

        // Admins own the platform, not a school - nothing to onboard.
        if (isAdmin()) {
            return $next($request);
        }

        // Parents, Students, Teachers and Accountants are attached to a
        // school by someone else (a Director) - they never own an
        // institution themselves, so they must never be forced through
        // onboarding.
        if ($user->hasAnyRole(['Parent', 'Student', 'Teacher', 'Accountant'])) {
            return $next($request);
        }

        // Anyone else without an institution yet (a brand new sign-up, or a
        // Director who hasn't registered their school) is sent to onboard.
        if ($user->institution()->count() === 0) {
            return redirect()->route('institution.create');
        }

        return $next($request);
    }
}
