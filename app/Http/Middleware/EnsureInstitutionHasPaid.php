<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureInstitutionHasPaid
{
    /**
     * Locks a Director out of the rest of the dashboard once their
     * grace period ends without a paid, active plan on file. The
     * institution's own profile and the billing page itself stay
     * reachable so they can still see their status and pay.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->is('dashboard*') || $request->is('dashboard/institutions*') || $request->is('dashboard/billing*')) {
            return $next($request);
        }

        if (institutionHasPaid() || institutionInPaymentGracePeriod()) {
            return $next($request);
        }

        return redirect()->route('billing.show')
            ->with('warning', 'Your free trial has ended. Please complete payment to keep using the system.');
    }
}
