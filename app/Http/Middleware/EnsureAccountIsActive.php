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
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && ! isAdmin() && auth()->user()->status !== 'active') {
            auth()->logout();

            return redirect()->route('login')->with('error', 'Your account is not activated. Please contact support. '.env('SUPPORT_PHONE'));
        }

        return $next($request);
    }
}
