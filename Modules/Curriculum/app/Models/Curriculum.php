<?php

namespace Modules\Curriculum\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Institution\Models\Institution;

// use Modules\Curriculum\Database\Factories\CurriculumFactory;

class Curriculum extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['name', 'status'];

    // protected static function newFactory(): CurriculumFactory
    // {
    //     // return CurriculumFactory::new();
    // }

    public function institutions()
    {
        return $this->hasMany(Institution::class, 'curriculum');
    }
}
