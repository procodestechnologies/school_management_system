<?php

namespace App\Models;

use Athwari\LaravelZktecoAdms\Models\ZktecoDevice;
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
        'zkteco_device_id',
        'is_active',
    ];
    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    public function zktecoDevice()
    {
        return $this->belongsTo(ZktecoDevice::class);
    }
}
