<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesLandlordConnection;

class Tenant extends Model
{
    use UsesLandlordConnection;

    protected $fillable = [
        'name',
        'domain',
        'database',
    ];
}
