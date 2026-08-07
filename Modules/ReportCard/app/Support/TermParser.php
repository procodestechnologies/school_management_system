<?php

namespace Modules\ReportCard\Support;

/**
 * Terms are stored as free text ("Second Term", "Term 2 2026", "2nd Term")
 * with no structured term number anywhere in the schema. This maps the
 * common phrasings used across the app to a canonical 1/2/3 so report
 * cards can be ordered and compared within an academic year.
 */
class TermParser
{
    private const PATTERNS = [
        1 => '/\b(first|1st|term\s*1|term\s*one)\b/i',
        2 => '/\b(second|2nd|term\s*2|term\s*two)\b/i',
        3 => '/\b(third|3rd|term\s*3|term\s*three)\b/i',
    ];

    public static function number(?string $term): ?int
    {
        if (! $term) {
            return null;
        }

        foreach (self::PATTERNS as $number => $pattern) {
            if (preg_match($pattern, $term) === 1) {
                return $number;
            }
        }

        return null;
    }
}
