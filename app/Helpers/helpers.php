<?php

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Modules\Institution\Models\Institution;

if (!function_exists('institutionOwner')) {
    function institutionOwner(int $institution)
    {
        return $institution === Auth::user()->id;
    }
}
if (!function_exists('progress')) {
    function progress(Institution $institution)
    {
        $institutionColumns = $institution->toArray();
        $columns = count($institutionColumns);
        $filled = Arr::where($institutionColumns, fn($value) => filled($value));
        return  round((count($filled) / $columns) * 100, 0);
    }
}
if (!function_exists('hasInstitutions')) {
    function hasInstitutions(): bool
    {
        $user = Auth::user();

        if (!$user) {
            return false;
        }

        // Admins always have access
        if (isAdmin()) {
            return true;
        }

        return $user->institution()->exists();
    }
}
if (!function_exists('isAdmin')) {
    function isAdmin()
    {
        return Auth::user()->hasRole('Admin');
    }
}
