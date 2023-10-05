<?php

namespace App\Console;

use App\Jobs\DispatchEveryDayEventJob;
use App\Jobs\DispatchEveryFiveMinutesEventJob;
use App\Jobs\DispatchEveryHourEventJobs;
use App\Jobs\DispatchEveryMinuteEventJob;
use App\Jobs\DispatchEveryTenMinutesEventJob;
use App\Jobs\DispatchMonthlyEventJob;
use App\Jobs\DispatchWeeklyEventJob;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param Schedule $schedule
     *
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->job(new DispatchEveryMinuteEventJob())->everyMinute();
        $schedule->job(new DispatchEveryFiveMinutesEventJob())->everyFiveMinutes();
        $schedule->job(new DispatchEveryTenMinutesEventJob())->everyTenMinutes();
        $schedule->job(new DispatchEveryHourEventJobs())->hourly();
        $schedule->job(new DispatchEveryDayEventJob())->dailyAt('22:00');
        $schedule->job(new DispatchWeeklyEventJob())->weekly();
        $schedule->job(new DispatchMonthlyEventJob())->monthly();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
