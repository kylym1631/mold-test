<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Http\Controllers\CronController;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call('App\Http\Controllers\CronController@setAuto7days')->daily();
        $schedule->call('App\Http\Controllers\CronController@setDeclineFreelance')->everyTwoHours();
        $schedule->call('App\Http\Controllers\CronController@importLeads')->everyThreeMinutes();
        $schedule->call('App\Http\Controllers\CronController@resetLeadTasks')->hourly();
        $schedule->call('App\Http\Controllers\CronController@distributeLeadsToUsers')->everyMinute();
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
