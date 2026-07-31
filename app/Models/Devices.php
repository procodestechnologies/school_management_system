<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Institution\Models\Institution;

class Devices extends Model
{
    /** @use HasFactory<\Database\Factories\DevicesFactory> */
    use HasFactory;
    protected $fillable = [
        'serial_number',
        'institution_id',
    ];
    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }
}
