<?php

namespace Modules\Curriculum\Models;

use App\Concerns\TetherSyncable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Institution\Models\Institution;

// use Modules\Curriculum\Database\Factories\CurriculumFactory;

class Curriculum extends Model
{
    use HasFactory, TetherSyncable;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['institution_id', 'name', 'status'];

    // protected static function newFactory(): CurriculumFactory
    // {
    //     // return CurriculumFactory::new();
    // }

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }
}
