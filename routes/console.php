<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
Schedule::command('feemanagement:send-reminders')->everyMinute();
Schedule::command('reportcards:send-ready')->everyMinute();

// School day runs 8am-4pm (Timetable module) - give teachers a couple of
// hours' buffer to finish marking attendance before tallying the day.
Schedule::command('lesson:generate-daily-reports')->dailyAt('18:00');
// The timetable only runs Monday-Friday, so Friday evening is "end of week".
Schedule::command('lesson:generate-weekly-reports')->weeklyOn(5, '18:30');

// A terminal stamps every punch with its own clock, so drift files
// attendance at the wrong hour without ever announcing itself. Hourly is
// far more often than any device drifts; it is a tiny queued command, and
// devices that are switched off are caught on reconnect instead.
Schedule::command('zkteco:sync-clocks')->hourly();
