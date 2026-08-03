<?php

namespace Modules\ReportCard\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Institution\Models\Institution;

class GradingBand extends Model
{
    use HasFactory;

    protected $fillable = [
        'institution_id',
        'min_percentage',
        'max_percentage',
        'grade',
        'remark',
    ];

    protected $casts = [
        'min_percentage' => 'decimal:2',
        'max_percentage' => 'decimal:2',
    ];

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }
}
