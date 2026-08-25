<?php

namespace Modules\Curriculum\Models;

use App\Concerns\TetherSyncable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Institution\Models\Institution;
use Modules\ReportCard\Models\GradingBand;

// use Modules\Curriculum\Database\Factories\CurriculumFactory;

class Curriculum extends Model
{
    use HasFactory, TetherSyncable;

    /**
     * The grading systems a curriculum can be run on: Kenya's 8-4-4, which
     * grades A-E, or CBC, which uses the four-band EE/ME/AE/BE rubric.
     *
     * @var array<string, string>
     */
    public const SYSTEMS = [
        '844' => '8-4-4',
        'cbc' => 'CBC',
    ];

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['institution_id', 'name', 'system', 'status'];

    // protected static function newFactory(): CurriculumFactory
    // {
    //     // return CurriculumFactory::new();
    // }

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    public function gradingBands()
    {
        return $this->hasMany(GradingBand::class);
    }

    /**
     * Whether results on this curriculum are graded on the CBC four-band
     * rubric rather than 8-4-4 letter grades.
     */
    public function isCbc(): bool
    {
        return $this->system === 'cbc';
    }

    public function systemLabel(): string
    {
        return self::SYSTEMS[$this->system] ?? (string) $this->system;
    }
}
