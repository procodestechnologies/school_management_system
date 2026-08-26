<?php

namespace Modules\ReportCard\Support;

use Modules\Curriculum\Models\Curriculum;

/**
 * The grading scales Kenyan schools actually use, ready to be loaded into
 * an institution in one click instead of typed in band by band.
 *
 * Bands stop at .99 rather than meeting at a whole number so the table
 * reads the way a school expects ("75 - 79.99, B+"). GradingBandService
 * rounds a percentage to two decimals before looking it up, which is what
 * keeps the sliver between one band's ceiling and the next one's floor
 * from falling through un-graded.
 *
 * None of these is law: each is a starting point a school edits from its
 * own settings page.
 */
class GradingScaleDefaults
{
    /**
     * Keys are `system:variant` throughout, and none is a bare number:
     * PHP silently casts a numeric string array key to an int, so a plain
     * '844' would come back out of array_keys() as int 844 and fail any
     * strict comparison against the constant.
     */
    public const SCALE_844 = '844:letters';

    public const SCALE_CBC_RUBRIC = 'cbc:rubric';

    public const SCALE_CBC_KJSEA = 'cbc:kjsea';

    /**
     * The standard scales a school can load, as one flat list.
     *
     * Flat because these are three different ways of grading, not a system
     * with a sub-setting: 8-4-4 marks A-E and has no variants, while CBC
     * marks against expectations in either four bands or KJSEA's eight
     * levels. Presenting them as one choice is what they are to the person
     * picking - "which scale does this school mark on".
     *
     * @return array<string, string>
     */
    public static function options(): array
    {
        return [
            self::SCALE_844 => '8-4-4 — A to E',
            self::SCALE_CBC_RUBRIC => 'CBC — 4-Band Rubric (EE / ME / AE / BE)',
            self::SCALE_CBC_KJSEA => 'CBC — KJSEA 8 Levels (EE1 to BE2)',
        ];
    }

    /**
     * The bands behind one of those options.
     *
     * @return array<int, array{min_percentage: float, max_percentage: float, grade: string, points: int, remark: string}>
     */
    public static function forKey(string $key): array
    {
        return match ($key) {
            self::SCALE_CBC_RUBRIC => self::cbcRubric(),
            self::SCALE_CBC_KJSEA => self::kjsea(),
            default => self::eightFourFour(),
        };
    }

    public static function labelForKey(string $key): string
    {
        return self::options()[$key] ?? self::options()[self::SCALE_844];
    }

    /**
     * Which option a curriculum already says it grades on - the one to
     * preselect so the common case is a single click.
     */
    public static function keyFor(?Curriculum $curriculum): string
    {
        if (! $curriculum?->isCbc()) {
            return self::SCALE_844;
        }

        return $curriculum->isKjsea() ? self::SCALE_CBC_KJSEA : self::SCALE_CBC_RUBRIC;
    }

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
     * CBC, marked on the four-band rubric: the scale a classroom teacher
     * uses through the term. It asks whether a learner has mastered the
     * task, not how they rank against the rest of the class, so the points
     * here are the performance levels 4-1 rather than grade points to be
     * aggregated into a mean grade.
     *
     * @return array<int, array{min_percentage: float, max_percentage: float, grade: string, points: int, remark: string}>
     */
    public static function cbcRubric(): array
    {
        return [
            ['min_percentage' => 80, 'max_percentage' => 100, 'grade' => 'EE', 'points' => 4, 'remark' => 'Exceeding Expectations - correctly performs all expected activities'],
            ['min_percentage' => 60, 'max_percentage' => 79.99, 'grade' => 'ME', 'points' => 3, 'remark' => 'Meeting Expectations - follows instructions and completes most activities'],
            ['min_percentage' => 40, 'max_percentage' => 59.99, 'grade' => 'AE', 'points' => 2, 'remark' => 'Approaching Expectations - attempts the work but is inconsistent'],
            ['min_percentage' => 0, 'max_percentage' => 39.99, 'grade' => 'BE', 'points' => 1, 'remark' => 'Below Expectations - major inaccuracies, or unable to complete tasks'],
        ];
    }

    /**
     * CBC, marked on the KJSEA achievement scale junior school reports
     * against from 2025: the same four bands, each split in two, giving
     * eight levels. `points` is the achievement level 8-1 that a KJSEA
     * report states per subject.
     *
     * The published ranges are whole numbers running 90-100, 75-89, 58-74
     * and so on, and start at 1 rather than 0. They're written here closing
     * at .99 and opening at 0 so a percentage landing between two of them -
     * 89.4, or an unattempted 0 - still resolves to a level instead of
     * coming back ungraded.
     *
     * @return array<int, array{min_percentage: float, max_percentage: float, grade: string, points: int, remark: string}>
     */
    public static function kjsea(): array
    {
        return [
            ['min_percentage' => 90, 'max_percentage' => 100, 'grade' => 'EE1', 'points' => 8, 'remark' => 'Exceptional'],
            ['min_percentage' => 75, 'max_percentage' => 89.99, 'grade' => 'EE2', 'points' => 7, 'remark' => 'Very good'],
            ['min_percentage' => 58, 'max_percentage' => 74.99, 'grade' => 'ME1', 'points' => 6, 'remark' => 'Good'],
            ['min_percentage' => 41, 'max_percentage' => 57.99, 'grade' => 'ME2', 'points' => 5, 'remark' => 'Fair'],
            ['min_percentage' => 31, 'max_percentage' => 40.99, 'grade' => 'AE1', 'points' => 4, 'remark' => 'Needs improvement'],
            ['min_percentage' => 21, 'max_percentage' => 30.99, 'grade' => 'AE2', 'points' => 3, 'remark' => 'Below average'],
            ['min_percentage' => 11, 'max_percentage' => 20.99, 'grade' => 'BE1', 'points' => 2, 'remark' => 'Well below average'],
            ['min_percentage' => 0, 'max_percentage' => 10.99, 'grade' => 'BE2', 'points' => 1, 'remark' => 'Minimal'],
        ];
    }

    /**
     * The default scale for a curriculum: which system it's taught on, and
     * for CBC, which of its two scales the school marks against.
     *
     * @return array<int, array{min_percentage: float, max_percentage: float, grade: string, points: int, remark: string}>
     */
    public static function for(Curriculum $curriculum): array
    {
        return self::forSystem((string) $curriculum->system, $curriculum->gradingScheme());
    }

    /**
     * @return array<int, array{min_percentage: float, max_percentage: float, grade: string, points: int, remark: string}>
     */
    public static function forSystem(string $system, ?string $scheme = null): array
    {
        if ($system !== 'cbc') {
            return self::eightFourFour();
        }

        return $scheme === Curriculum::SCHEME_KJSEA ? self::kjsea() : self::cbcRubric();
    }

    /**
     * How to name a scale in the confirmation the settings page shows once
     * it has been loaded.
     */
    public static function labelFor(string $system, ?string $scheme = null): string
    {
        if ($system !== 'cbc') {
            return '8-4-4 grading scale';
        }

        return $scheme === Curriculum::SCHEME_KJSEA
            ? 'KJSEA eight-level achievement scale'
            : 'CBC four-band rubric';
    }
}
