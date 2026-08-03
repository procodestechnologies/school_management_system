<?php

namespace Modules\ReportCard\Services;

use Modules\Institution\Models\Institution;
use Modules\ReportCard\Models\GradingBand;

class GradingBandService
{
    /**
     * Look up the letter grade for a percentage score, per the
     * institution's own grading bands. Returns null if no bands are
     * configured yet, or none of them cover the given percentage.
     */
    public static function resolve(Institution $institution, float $percentage): ?string
    {
        $band = GradingBand::where('institution_id', $institution->id)
            ->where('min_percentage', '<=', $percentage)
            ->where('max_percentage', '>=', $percentage)
            ->orderByDesc('min_percentage')
            ->first();

        return $band?->grade;
    }
}
