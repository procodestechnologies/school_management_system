<?php

namespace Modules\Attendance\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// use Modules\Attendance\Database\Factories\AttendanceFactory;

class Attendance extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    // protected static function newFactory(): AttendanceFactory
    // {
    //     // return AttendanceFactory::new();
    // }

    public function attendances() {}
}
