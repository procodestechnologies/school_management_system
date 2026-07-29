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
