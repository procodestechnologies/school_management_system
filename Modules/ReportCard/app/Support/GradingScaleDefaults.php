<?php

namespace Modules\ReportCard\Support;

use Modules\Curriculum\Models\Curriculum;

/**
 * The two grading scales Kenyan schools actually use, ready to be loaded
 * into an institution in one click instead of typed in band by band.
 *
 * Bands stop at .99 rather than meeting at a whole number so the table
 * reads the way a school expects ("75 - 79.99, B+"). GradingBandService
 * rounds a percentage to two decimals before looking it up, which is what
 * keeps the sliver between one band's ceiling and the next one's floor
 * from falling through un-graded.
 *
 * Neither scale is law: both are a starting point a school edits from its
 * own settings page.
 */
class GradingScaleDefaults
{
    /**
     * 8-4-4: twelve letter grades, each worth the KCSE grade points used
     * to work out a mean grade.
     *
     * @return array<int, array{min_percentage: float, max_percentage: float, grade: string, points: int, remark: string}>
     */
    public static function eightFourFour(): array
    {
        return [
            ['min_percentage' => 80, 'max_percentage' => 100, 'grade' => 'A', 'points' => 12, 'remark' => 'Excellent'],
            ['min_percentage' => 75, 'max_percentage' => 79.99, 'grade' => 'A-', 'points' => 11, 'remark' => 'Very good'],
            ['min_percentage' => 70, 'max_percentage' => 74.99, 'grade' => 'B+', 'points' => 10, 'remark' => 'Very good'],
            ['min_percentage' => 65, 'max_percentage' => 69.99, 'grade' => 'B', 'points' => 9, 'remark' => 'Good'],
            ['min_percentage' => 60, 'max_percentage' => 64.99, 'grade' => 'B-', 'points' => 8, 'remark' => 'Good'],
            ['min_percentage' => 55, 'max_percentage' => 59.99, 'grade' => 'C+', 'points' => 7, 'remark' => 'Above average'],
            ['min_percentage' => 50, 'max_percentage' => 54.99, 'grade' => 'C', 'points' => 6, 'remark' => 'Average'],
            ['min_percentage' => 45, 'max_percentage' => 49.99, 'grade' => 'C-', 'points' => 5, 'remark' => 'Average'],
            ['min_percentage' => 40, 'max_percentage' => 44.99, 'grade' => 'D+', 'points' => 4, 'remark' => 'Below average'],
            ['min_percentage' => 35, 'max_percentage' => 39.99, 'grade' => 'D', 'points' => 3, 'remark' => 'Weak'],
            ['min_percentage' => 30, 'max_percentage' => 34.99, 'grade' => 'D-', 'points' => 2, 'remark' => 'Weak'],
            ['min_percentage' => 0, 'max_percentage' => 29.99, 'grade' => 'E', 'points' => 1, 'remark' => 'Needs serious improvement'],
        ];
    }

    /**
     * CBC: the four-band rubric. It asks whether a learner has mastered the
     * task, not how they rank against the rest of the class, so the points
     * here are the performance levels 4-1 rather than grade points to be
     * aggregated into a mean.
     *
     * @return array<int, array{min_percentage: float, max_percentage: float, grade: string, points: int, remark: string}>
     */
    public static function cbc(): array
    {
        return [
            ['min_percentage' => 80, 'max_percentage' => 100, 'grade' => 'EE', 'points' => 4, 'remark' => 'Exceeding Expectations - exceptional understanding and performance'],
            ['min_percentage' => 60, 'max_percentage' => 79.99, 'grade' => 'ME', 'points' => 3, 'remark' => 'Meeting Expectations - follows instructions and completes most activities successfully'],
            ['min_percentage' => 40, 'max_percentage' => 59.99, 'grade' => 'AE', 'points' => 2, 'remark' => 'Approaching Expectations - attempts work but requires support'],
            ['min_percentage' => 0, 'max_percentage' => 39.99, 'grade' => 'BE', 'points' => 1, 'remark' => 'Below Expectations - requires significant intervention'],
        ];
    }

    /**
     * The default scale for a curriculum, chosen by the system it runs on.
     *
     * @return array<int, array{min_percentage: float, max_percentage: float, grade: string, points: int, remark: string}>
     */
    public static function for(Curriculum $curriculum): array
    {
        return self::forSystem((string) $curriculum->system);
    }

    /**
     * @return array<int, array{min_percentage: float, max_percentage: float, grade: string, points: int, remark: string}>
     */
    public static function forSystem(string $system): array
    {
        return $system === 'cbc' ? self::cbc() : self::eightFourFour();
    }
}
