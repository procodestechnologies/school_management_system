<?php

namespace Modules\Lesson\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Classes\Models\SchoolClass;
use Modules\Institution\Models\Institution;
use Modules\Timetable\Models\TimetableEntry;

class Lesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'institution_id',
        'class_id',
        'timetable_entry_id',
        'lesson_date',
        'status',
        'remarks',
        'marked_by',
    ];

    protected $casts = [
        'lesson_date' => 'date',
    ];

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function timetableEntry()
    {
        return $this->belongsTo(TimetableEntry::class);
    }

    public function markedBy()
    {
        return $this->belongsTo(User::class, 'marked_by');
    }

    public function isAttended(): bool
    {
        return $this->status === 'attended';
    }
}
