<?php

namespace Modules\ReportCard\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Classes\Models\SchoolClass;
use Modules\Institution\Models\Institution;

class ReportCard extends Model
{
    use HasFactory;

    protected $fillable = [
        'institution_id',
        'student_id',
        'class_id',
        'term',
        'mean_percentage',
        'mean_grade',
        'status',
        'pdf_path',
        'completed_at',
        'sent_at',
    ];

    protected $casts = [
        'mean_percentage' => 'decimal:2',
        'completed_at' => 'datetime',
        'sent_at' => 'datetime',
    ];

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function isSent(): bool
    {
        return $this->status === 'sent';
    }
}
