<?php

namespace Modules\Examinations\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Institution\Models\Institution;

class Examination extends Model
{
    use HasFactory;

    protected $fillable = [
        'institution_id',
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
}
