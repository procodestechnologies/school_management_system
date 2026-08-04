<?php

namespace Modules\Lesson\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Classes\Models\SchoolClass;
use Modules\Institution\Models\Institution;

class LessonReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'institution_id',
        'class_id',
        'type',
        'period_start',
        'period_end',
        'total_lessons',
        'attended_count',
        'not_attended_count',
        'recovered_count',
        'pdf_path',
        'generated_by',
        'generated_at',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'generated_at' => 'datetime',
    ];

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function generatedBy()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    public function isWeekly(): bool
    {
        return $this->type === 'weekly';
    }
}
