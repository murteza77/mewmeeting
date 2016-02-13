<?php

namespace App\Console;

use App\Http\Controllers\AccountController;
use App\Http\Controllers\UserController;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\Inspire::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $reminderEmail = new UserController();
        $dailyEventList = new AccountController();

        $schedule->call(function() use ($reminderEmail, $dailyEventList){
            $reminderEmail->getSendReminderEmail();
            $dailyEventList->getMailMe();
        })->daily();

        /*$schedule->command('inspire')
                 ->hourly();*/
    }
}
