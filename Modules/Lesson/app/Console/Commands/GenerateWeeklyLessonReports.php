<?php

namespace Modules\Lesson\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Modules\Classes\Models\SchoolClass;
use Modules\Lesson\Models\LessonReport;
use Modules\Lesson\Services\LessonReportPdfService;
use Modules\Lesson\Services\LessonReportService;

class GenerateWeeklyLessonReports extends Command
{
    protected $signature = 'lesson:generate-weekly-reports {--week=}';

    protected $description = "Generate each class's end-of-week lesson attendance report and PDF";

    public function handle(LessonReportService $reportService, LessonReportPdfService $pdfService): int
    {
        $anchor = $this->option('week') ? Carbon::parse($this->option('week')) : Carbon::today();
        $weekStart = $anchor->copy()->startOfWeek(Carbon::MONDAY);
        $weekEnd = $weekStart->copy()->addDays(4); // Friday - the timetable only runs Monday-Friday

        $generated = 0;

        foreach (SchoolClass::all() as $class) {
            $stats = $reportService->compute($class, $weekStart->copy(), $weekEnd->copy());

            if ($stats['total'] === 0) {
                continue;
            }

            $report = LessonReport::updateOrCreate(
                ['class_id' => $class->id, 'type' => 'weekly', 'period_start' => $weekStart->toDateString()],
                [
                    'institution_id' => $class->institution_id,
                    'period_end' => $weekEnd->toDateString(),
                    'total_lessons' => $stats['total'],
                    'attended_count' => $stats['attended'],
                    'not_attended_count' => $stats['notAttended'],
                    'recovered_count' => $stats['recovered'],
                    'generated_at' => now(),
                ]
            );

            $pdfService->generate($report);
            $generated++;
        }

        $this->info("Generated {$generated} weekly lesson report(s) (with PDF) for week of {$weekStart->toDateString()}.");

        return self::SUCCESS;
    }
}
