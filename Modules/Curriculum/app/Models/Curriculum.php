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
     * The systems a curriculum can be taught on: Kenya's 8-4-4, which
     * grades A-E, or CBC, which grades against expectations.
     *
     * @var array<string, string>
     */
    public const SYSTEMS = [
        '844' => '8-4-4',
        'cbc' => 'CBC',
    ];

    /**
     * CBC's classroom rubric: the four bands EE/ME/AE/BE, levels 4-1.
     */
    public const SCHEME_RUBRIC = 'rubric';

    /**
     * CBC's KJSEA achievement scale: those same four bands each split in
     * two, giving the eight levels EE1-BE2 that junior school reports
     * against from 2025.
     */
    public const SCHEME_KJSEA = 'kjsea';

    /**
     * How a CBC curriculum is marked. Both are CBC - the rubric is what a
     * teacher grades classwork on, KJSEA is what junior school reports at -
     * so a school picks the one matching the level it teaches rather than
     * treating them as rival curricula.
     *
     * 8-4-4 has no equivalent choice: A-E is the only way it's graded.
     *
     * @var array<string, string>
     */
    public const SCHEMES = [
        self::SCHEME_RUBRIC => '4-Band Rubric (EE / ME / AE / BE)',
        self::SCHEME_KJSEA => 'KJSEA 8-Level Scale (EE1 - BE2)',
    ];

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['institution_id', 'name', 'system', 'grading_scheme', 'status'];

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
     * Whether results on this curriculum are graded against expectations
     * rather than on 8-4-4 letter grades.
     */
    public function isCbc(): bool
    {
        return $this->system === 'cbc';
    }

    /**
     * Which CBC scale this curriculum is marked on. Null on 8-4-4, where
     * there is nothing to choose. CBC rows saved before the scheme existed
     * carry no value, and fall back to the rubric they were loaded with.
     */
    public function gradingScheme(): ?string
    {
        if (! $this->isCbc()) {
            return null;
        }

        return array_key_exists((string) $this->grading_scheme, self::SCHEMES)
            ? (string) $this->grading_scheme
            : self::SCHEME_RUBRIC;
    }

    /**
     * Whether this curriculum is marked on KJSEA's eight achievement
     * levels rather than the four-band rubric.
     */
    public function isKjsea(): bool
    {
        return $this->gradingScheme() === self::SCHEME_KJSEA;
    }

    public function systemLabel(): string
    {
        return self::SYSTEMS[$this->system] ?? (string) $this->system;
    }

    /**
     * The curriculum's scale, named for a heading or a badge - "CBC" alone
     * doesn't say which of its two a school marks against.
     */
    public function schemeLabel(): ?string
    {
        $scheme = $this->gradingScheme();

        return $scheme === null ? null : self::SCHEMES[$scheme];
    }

    /**
     * System and scale together, for the one-line label the settings and
     * curriculum screens show.
     */
    public function gradingLabel(): string
    {
        return $this->isKjsea()
            ? $this->systemLabel().' · KJSEA'
            : ($this->isCbc() ? $this->systemLabel().' · Rubric' : $this->systemLabel());
    }
}
