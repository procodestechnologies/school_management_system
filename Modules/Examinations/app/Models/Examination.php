<?php

namespace Modules\Examinations\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Classes\Models\SchoolClass;
use Modules\Institution\Models\Institution;
use Modules\Result\Models\Result;

class Examination extends Model
{
    use HasFactory;

    protected $fillable = [
        'institution_id',
        'class_id',
        'title',
        'subject',
        'class_name',
        'term',
        'exam_date',
        'start_time',
        'end_time',
        'total_marks',
        'passing_marks',
        'notes',
    ];

    protected $casts = [
        'exam_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
    ];

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function results()
    {
        return $this->hasMany(Result::class);
    }
}
