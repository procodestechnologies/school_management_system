<?php

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Modules\Institution\Models\Institution;

if (! function_exists('institutionOwner')) {
    function institutionOwner(int $institution)
    {
        return $institution === Auth::user()->id;
    }
}
if (! function_exists('progress')) {
    function progress(Institution $institution)
    {
        $institutionColumns = $institution->toArray();
        $columns = count($institutionColumns);
        $filled = Arr::where($institutionColumns, fn ($value) => filled($value));

        return round((count($filled) / $columns) * 100, 0);
    }
}
if (! function_exists('hasInstitutions')) {
    function hasInstitutions(): bool
    {
        $user = Auth::user();

        if (! $user) {
            return false;
        }

        // Admins own the platform, not a school.
        if (isAdmin()) {
            return true;
        }

        // Parents, Students, Teachers and Accountants belong to a school
        // without ever owning one themselves - they always have access to
        // its data.
        if ($user->hasAnyRole(['Parent', 'Student', 'Teacher', 'Accountant'])) {
            return true;
        }

        return $user->institution()->exists();
    }
}
if (! function_exists('isAdmin')) {
    function isAdmin()
    {
        return Auth::user()->hasRole('Admin');
    }
}
if (! function_exists('isDirector')) {
    function isDirector()
    {
        return Auth::user()->hasRole('Director');
    }
}
if (! function_exists('institutionApproved')) {
    function institutionApproved(): bool
    {
        $user = Auth::user();

        if (! $user || isAdmin()) {
            return true;
        }

        // Staff/dependents never own an institution and can't exist yet
        // under a still-pending Director, so nothing to gate for them.
        if ($user->hasAnyRole(['Parent', 'Student', 'Teacher', 'Accountant'])) {
            return true;
        }

        $institution = currentInstitution();

        // No active institution yet (onboarding, or not chosen among
        // several) is handled separately by HasInstitution - not an
        // "unapproved" case for this helper.
        if (! $institution) {
            return true;
        }

        return (bool) $institution->is_approved;
    }
}
if (! function_exists('currentInstitution')) {
    function currentInstitution(): ?Institution
    {
        $user = Auth::user();

        if (! $user || isAdmin()) {
            return null;
        }

        if ($user->hasRole('Student')) {
            return $user->studentInstitution;
        }

        if ($user->hasRole('Teacher')) {
            return $user->teacherUserDetails?->institution;
        }

        if ($user->hasRole('Parent')) {
            return $user->parentInstitution;
        }

        // Director (and any other role that owns institutions directly) -
        // whichever one they've chosen as active, not just "the first
        // one" - see HasInstitution for how/when this gets set.
        return $user->activeInstitution;
    }
}
if (! function_exists('institutionHasModule')) {
    function institutionHasModule(string $module): bool
    {
        // Admins aren't scoped to an institution's plan, and an
        // unresolvable institution (e.g. an Accountant with no plan
        // linkage yet) shouldn't be locked out over gaps in that lookup.
        $institution = currentInstitution();

        if (isAdmin() || ! $institution) {
            return true;
        }

        return $institution->hasModule($module);
    }
}
if (! function_exists('institutionHasPaid')) {
    function institutionHasPaid(): bool
    {
        $user = Auth::user();

        if (! $user || isAdmin()) {
            return true;
        }

        // Staff/dependents never own the subscription - paying for it is
        // the Director's/institution's concern, not theirs.
        if ($user->hasAnyRole(['Parent', 'Student', 'Teacher', 'Accountant'])) {
            return true;
        }

        $institution = currentInstitution();

        // No institution yet is the onboarding case, handled by
        // HasInstitution - not an "unpaid" case for this helper.
        if (! $institution) {
            return true;
        }

        return $institution->hasActiveSubscription();
    }
}
if (! function_exists('institutionInPaymentGracePeriod')) {
    function institutionInPaymentGracePeriod(): bool
    {
        $institution = currentInstitution();

        if (! $institution) {
            return true;
        }

        // Grace period counts from whichever happened most recently: the
        // institution's last paid plan expiring, or (for one that's never
        // paid at all) its approval date.
        $anchor = $institution->subscription_expires_at ?? $institution->approved_at ?? $institution->created_at;

        return ! $anchor || now()->lessThanOrEqualTo($anchor->copy()->addDays(14));
    }
}
if (! function_exists('institutionCanUpgrade')) {
    function institutionCanUpgrade(): bool
    {
        $institution = currentInstitution();

        if (! $institution) {
            return false;
        }

        $currentPrice = $institution->hasActiveSubscription() ? (float) $institution->plan->price : -1;

        return \App\Models\Plan::where('is_active', true)->where('price', '>', $currentPrice)->exists();
    }
}
