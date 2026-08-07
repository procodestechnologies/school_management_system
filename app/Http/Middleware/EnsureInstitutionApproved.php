<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureInstitutionApproved
{
    /**
     * Locks a Director out of the rest of the dashboard until an Admin
     * approves their self-created institution. Institution's own routes
     * stay reachable so a pending Director can still view/edit their
     * school's profile and see its approval status.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->is('dashboard*') || $request->is('dashboard/institutions*')) {
            return $next($request);
        }

        if (! institutionApproved()) {
            return redirect()->route('institution.show', currentInstitution()->id)
                ->with('warning', 'Your institution is awaiting Admin approval. Most features will unlock once approved.');
        }

        return $next($request);
    }
}
