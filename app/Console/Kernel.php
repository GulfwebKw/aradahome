<?php

namespace App\Console;

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
        Commands\SmsCron::class,
        Commands\AddCountry::class,
		Commands\RemoveLogsCron::class,
		Commands\WebNotificationCron::class,
		Commands\RollBackQtyForFailedPayment::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('sms:cron')
                 ->everyMinute();
		$schedule->command('logs:cron')
                 ->daily();
		$schedule->command('QuantityAlert:cron')
                 ->everyMinute();		
		$schedule->command('rollbackqty:cron')
                 ->everyMinute();		  
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
	    $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}
