<?php

namespace Modules\Lesson\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Modules\Classes\Models\SchoolClass;
use Modules\Lesson\Models\LessonReport;
use Modules\Lesson\Services\LessonReportService;

class GenerateDailyLessonReports extends Command
{
    protected $signature = 'lesson:generate-daily-reports {--date=}';

    protected $description = 'Tally each class\'s end-of-day lesson attendance (attended/not attended/recovered)';

    public function handle(LessonReportService $reportService): int
    {
        $date = $this->option('date') ? Carbon::parse($this->option('date')) : Carbon::today();

        $generated = 0;

        foreach (SchoolClass::all() as $class) {
            $stats = $reportService->compute($class, $date->copy(), $date->copy());

            if ($stats['total'] === 0) {
                continue;
            }

            LessonReport::updateOrCreate(
                ['class_id' => $class->id, 'type' => 'daily', 'period_start' => $date->toDateString()],
                [
                    'institution_id' => $class->institution_id,
                    'period_end' => $date->toDateString(),
                    'total_lessons' => $stats['total'],
                    'attended_count' => $stats['attended'],
                    'not_attended_count' => $stats['notAttended'],
                    'recovered_count' => $stats['recovered'],
                    'generated_at' => now(),
                ]
            );

            $generated++;
        }

        $this->info("Generated {$generated} daily lesson report(s) for {$date->toDateString()}.");

        return self::SUCCESS;
    }
}
