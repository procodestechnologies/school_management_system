<?php

namespace Modules\Institution\Models;

use App\Models\Devices;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Classes\Models\SchoolClass;
use Modules\Curriculum\Models\Curriculum;
use Modules\Examinations\Models\Examination;
use Modules\FeeManagement\Models\Fee;
use Modules\Lesson\Models\Lesson;
use Modules\Result\Models\Result;
use Modules\Student\Models\StudentDetails;
use Modules\Subject\Models\Subject;
use Modules\Teacher\Models\TeacherDetails;
use Modules\Timetable\Models\TimetableEntry;

// use Modules\Institution\Database\Factories\InstitutionFactory;

class Institution extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',

        // Basic Information
        'name',
        'code',
        'type',

        // Contact
        'email',
        'phone',
        'alternate_phone',
        'website',

        // Address
        'country',
        'county',
        'city',
        'postal_address',
        'physical_address',

        // Branding
        'logo',
        'favicon',

        // Administration
        'principal_name',
        'principal_phone',

        // Academic
        'curriculum',
        'education_level',

        // Attendance
        'timezone',

        // Status
        'status',
        'is_active',

        // Subscription
        'subscription_plan',
        'subscription_expires_at',

        // Optional
        'notes',
    ];

    // protected static function newFactory(): InstitutionFactory
    // {
    //     // return InstitutionFactory::new();
    // }
    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function devices()
    {
        return $this->hasMany(Devices::class);
    }

    public function students()
    {
        return $this->hasManyThrough(User::class, StudentDetails::class, 'user_id', 'id', 'id', 'institution_id');
    }

    public function parents()
    {
        return $this->hasManyThrough(
            User::class,            // The final model we want (Parent User)
            StudentDetails::class,  // The intermediate model
            'institution_id',       // Foreign key on student_details (matches institutions.id)
            'id',                   // Foreign key on users (matches student_details.parent_id)
            'id',                   // Local key on institutions (institutions.id)
            'parent_id'             // Local key on student_details (student_details.parent_id)
        )->whereHas('roles', function ($query) {
            $query->where('name', 'Parent'); // Only get users with Parent role
        });
    }

    public function fees()
    {
        return $this->hasMany(Fee::class);
    }

    public function curriculum()
    {
        return $this->belongsTo(Curriculum::class, 'curriculum');
    }

    public function teachers()
    {
        return $this->hasManyThrough(User::class, TeacherDetails::class, 'institution_id', 'id', 'id', 'teacher_id');
    }

    public function timetableEntries()
    {
        return $this->hasMany(TimetableEntry::class);
    }

    public function examinations()
    {
        return $this->hasMany(Examination::class);
    }

    public function classes()
    {
        return $this->hasMany(SchoolClass::class);
    }

    public function lessons()
    {
        return $this->hasMany(Lesson::class);
    }

    public function results()
    {
        return $this->hasMany(Result::class);
    }

    public function subjects()
    {
        return $this->hasMany(Subject::class);
    }
}
