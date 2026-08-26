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
     * The one-line label the settings and curriculum screens show.
     *
     * It names the grades themselves rather than just the system, because
     * the mistake this label has to make visible is a curriculum named for
     * one system that is set to grade on the other. "C.B.C — 8-4-4" reads
     * like a formatting quirk; "C.B.C — 8-4-4 (A to E)" reads like the
     * error it is.
     */
    public function gradingLabel(): string
    {
        if (! $this->isCbc()) {
            return $this->systemLabel().' (A to E)';
        }

        return $this->isKjsea()
            ? $this->systemLabel().' (EE1 to BE2)'
            : $this->systemLabel().' (EE / ME / AE / BE)';
    }

    /**
     * Whether the curriculum's name points at one system while `system`
     * says the other - "C.B.C" left grading A-E, for instance.
     *
     * Worth flagging rather than silently correcting: the name is free
     * text a school chose, and only they know which half is the mistake.
     * Punctuation and spaces are stripped first so "C.B.C" and "C B C" are
     * caught alongside "CBC", and "8-4-4" alongside "844" - digits have to
     * survive that strip, or the 8-4-4 half of the check never fires.
     */
    public function looksMisconfigured(): bool
    {
        $name = strtolower(preg_replace('/[^a-z0-9]/i', '', (string) $this->name) ?? '');

        if ($name === '') {
            return false;
        }

        $namedCbc = str_contains($name, 'cbc')
            || str_contains($name, 'cbe')
            || str_contains($name, 'competency');

        $named844 = str_contains($name, '844');

        return ($namedCbc && ! $this->isCbc()) || ($named844 && $this->isCbc());
    }
}
