<?php

namespace Modules\Lesson\Providers;

use Illuminate\Console\Scheduling\Schedule;
use Modules\Lesson\Console\Commands\GenerateDailyLessonReports;
use Modules\Lesson\Console\Commands\GenerateWeeklyLessonReports;
use Nwidart\Modules\Support\ModuleServiceProvider;

class LessonServiceProvider extends ModuleServiceProvider
{
    /**
     * The name of the module.
     */
    protected string $name = 'Lesson';

    /**
     * The lowercase version of the module name.
     */
    protected string $nameLower = 'lesson';

    /**
     * Command classes to register.
     *
     * @var string[]
     */
    protected array $commands = [
        GenerateDailyLessonReports::class,
        GenerateWeeklyLessonReports::class,
    ];

    /**
     * Provider classes to register.
     *
     * @var string[]
     */
    protected array $providers = [
        EventServiceProvider::class,
        RouteServiceProvider::class,
    ];

    /**
     * Define module schedules.
     *
     * @param  $schedule
     */
    // protected function configureSchedules(Schedule $schedule): void
    // {
    //     $schedule->command('inspire')->hourly();
    // }
}
