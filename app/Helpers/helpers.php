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
