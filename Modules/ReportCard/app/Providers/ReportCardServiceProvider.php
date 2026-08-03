<?php

namespace Modules\ReportCard\Providers;

use Nwidart\Modules\Support\ModuleServiceProvider;
use Illuminate\Console\Scheduling\Schedule;
use Modules\ReportCard\Console\Commands\SendReadyReportCards;

class ReportCardServiceProvider extends ModuleServiceProvider
{
    /**
     * The name of the module.
     */
    protected string $name = 'ReportCard';

    /**
     * The lowercase version of the module name.
     */
    protected string $nameLower = 'reportcard';

    /**
     * Command classes to register.
     *
     * @var string[]
     */
    protected array $commands = [
        SendReadyReportCards::class,
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
     * @param $schedule
     */
    // protected function configureSchedules(Schedule $schedule): void
    // {
    //     $schedule->command('inspire')->hourly();
    // }
}
