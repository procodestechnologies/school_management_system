<?php

namespace Modules\Institution\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use Modules\Institution\Database\Factories\InstitutionFactory;

class Institution extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',

        // Basic Information
        'name',
        'code',
        'type',

        // Contact
        'email',
        'phone',
        'alternate_phone',
        'website',

        // Address
        'country',
        'county',
        'city',
        'postal_address',
        'physical_address',

        // Branding
        'logo',
        'favicon',

        // Administration
        'principal_name',
        'principal_phone',

        // Academic
        'curriculum',
        'education_level',

        // Attendance
        'timezone',

        // Status
        'status',
        'is_active',

        // Subscription
        'subscription_plan',
        'subscription_expires_at',

        // Optional
        'notes',
    ];

    // protected static function newFactory(): InstitutionFactory
    // {
    //     // return InstitutionFactory::new();
    // }
    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
