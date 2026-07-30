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

        if (!$user) {
            return redirect()->route('login');
        }

        // Allow admins through
        if (isAdmin()) {
            return $next($request);
        }

        // If user has no institution, redirect them to create one
        if ($user->institution()->count() === 0) {
            return redirect()->route('institution.create');
        }

        return $next($request);
    }
}
