<?php

namespace Modules\Classes\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Examinations\Models\Examination;
use Modules\Institution\Models\Institution;
use Modules\Lesson\Models\Lesson;
use Modules\Result\Models\Result;
use Modules\Student\Models\StudentDetails;
use Modules\Timetable\Models\TimetableEntry;

class SchoolClass extends Model
{
    use HasFactory;

    /**
     * Table is "classes" - the model can't be named Class since that's a
     * reserved word in PHP.
     */
    protected $table = 'classes';

    protected $fillable = [
        'institution_id',
        'name',
        'level',
        'class_teacher_id',
        'capacity',
    ];

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    public function classTeacher()
    {
        return $this->belongsTo(User::class, 'class_teacher_id');
    }

    public function students()
    {
        return $this->hasMany(StudentDetails::class, 'class_id');
    }

    public function timetableEntries()
    {
        return $this->hasMany(TimetableEntry::class, 'class_id');
    }

    public function lessons()
    {
        return $this->hasMany(Lesson::class, 'class_id');
    }

    public function results()
    {
        return $this->hasMany(Result::class, 'class_id');
    }

    public function examinations()
    {
        return $this->hasMany(Examination::class, 'class_id');
    }
}
