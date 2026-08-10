<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAccountIsActive
{
    /**
     * Handle an incoming request.
     *
     * Signup is gated by email verification (User implements
     * MustVerifyEmail), so the only thing left for this to enforce is
     * suspension - an account an admin has deliberately switched off.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && ! isAdmin() && auth()->user()->status === 'suspended') {
            auth()->logout();

            return redirect()->route('login')->with('error', 'Your account has been suspended. Please contact support. '.env('SUPPORT_PHONE'));
        }

        return $next($request);
    }
}
