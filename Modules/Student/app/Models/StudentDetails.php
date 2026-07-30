<?php

namespace Modules\Student\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Student\Database\Factories\StudentDetailsFactory;

class StudentDetails extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        "phone",
        "date_of_birth",
        "gender",
        "admission_number",
        "student_number",
        "address",
        "city",
        "state",
        "country",
        "parent_name",
        "parent_phone",
        "parent_email",
        "parent_occupation",
        "guardian_name",
        "guardian_phone",
        "guardian_email",
        "guardian_relationship",
        "medical_conditions",
        "allergies",
        "special_needs",
        "is_active",
        "enrollment_status",
        "institution_id",
        "class_id",
        "profile_photo",
        "notes",
    ];

    // protected static function newFactory(): StudentDetailsFactory
    // {
    //     // return StudentDetailsFactory::new();
    // }
}
