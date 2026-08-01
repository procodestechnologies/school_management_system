<?php

namespace Modules\Parent\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// use Modules\Parent\Database\Factories\ParentDetailsFactory;

class ParentDetails extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'parent_id',
        'parent_phone',
        'parent_occupation',
    ];

    // protected static function newFactory(): ParentDetailsFactory
    // {
    //     // return ParentDetailsFactory::new();
    // }
    public function parent()
    {
        return $this->belongsTo(User::class);
    }
}
