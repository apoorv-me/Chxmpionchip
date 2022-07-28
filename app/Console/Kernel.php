<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Http\Controller\HomeController;
use App\Http\Controllers\API\V1\SportDataController;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
           $schedule->call('App\Http\Controllers\HomeController@addChips')->fridays()->at('01:00');
           $schedule->call('App\Http\Controllers\HomeController@getTeamsByLeagues')->daily();
           $schedule->call('App\Http\Controllers\HomeController@getGamesCron')->dailyAt('1:00');
           $schedule->call('App\Http\Controllers\HomeController@updateGameResult')->everyMinute();
           $schedule->call('App\Http\Controllers\API\V1\SportDataController@revertAmountPendingRequest')->twiceDaily(1, 13);
           
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
