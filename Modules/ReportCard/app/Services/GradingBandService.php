<?php

namespace Modules\ReportCard\Services;

use Modules\Classes\Models\SchoolClass;
use Modules\Curriculum\Models\Curriculum;
use Modules\Institution\Models\Institution;
use Modules\ReportCard\Models\GradingBand;

class GradingBandService
{
    /**
     * Look up the grade for a percentage score - a letter on 8-4-4, one of
     * EE/ME/AE/BE on CBC - per the scale the institution has configured for
     * that curriculum. Returns null if no bands are configured yet, or none
     * of them cover the given percentage.
     */
    public static function resolve(Institution $institution, float $percentage, ?int $curriculumId = null): ?string
    {
        return self::resolveBand($institution, $percentage, $curriculumId)?->grade;
    }

    /**
     * The band itself rather than just its grade, for callers that also
     * want the remark or the points behind it.
     *
     * A curriculum's own bands win; a school that hasn't split its scale by
     * curriculum falls back to its school-wide bands, which is what every
     * scale configured before curricula were separated still is.
     */
    public static function resolveBand(Institution $institution, float $percentage, ?int $curriculumId = null): ?GradingBand
    {
        // Two decimals is the resolution the bands themselves are written
        // at (..., 74.99, 75, ...), so rounding to it is what stops a score
        // landing in the sliver between two bands and coming back ungraded.
        $percentage = round($percentage, 2);

        if ($curriculumId) {
            $band = self::lookup($institution, $percentage, $curriculumId);

            if ($band) {
                return $band;
            }
        }

        return self::lookup($institution, $percentage, null);
    }

    /**
     * Which curriculum's scale a class is marked against: its own if one is
     * set, otherwise the school's only curriculum when it has exactly one.
     * Null once a school runs several and the class hasn't said which - the
     * school-wide bands then apply.
     */
    public static function curriculumIdFor(?SchoolClass $schoolClass, ?Institution $institution = null): ?int
    {
        if ($schoolClass?->curriculum_id) {
            return (int) $schoolClass->curriculum_id;
        }

        $institution ??= $schoolClass?->institution;

        if (! $institution) {
            return null;
        }

        $curricula = Curriculum::where('institution_id', $institution->id)
            ->where('status', 'active')
            ->pluck('id');

        return $curricula->count() === 1 ? (int) $curricula->first() : null;
    }

    private static function lookup(Institution $institution, float $percentage, ?int $curriculumId): ?GradingBand
    {
        return GradingBand::where('institution_id', $institution->id)
            ->when(
                $curriculumId,
                fn ($query) => $query->where('curriculum_id', $curriculumId),
                fn ($query) => $query->whereNull('curriculum_id'),
            )
            ->where('min_percentage', '<=', $percentage)
            ->where('max_percentage', '>=', $percentage)
            ->orderByDesc('min_percentage')
            ->first();
    }
}
