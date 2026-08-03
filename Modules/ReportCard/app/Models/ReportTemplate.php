<?php

namespace Modules\ReportCard\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Institution\Models\Institution;

class ReportTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'institution_id',
        'opening_text',
        'closing_text',
        'signatory_name',
        'signatory_title',
    ];

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }
}
