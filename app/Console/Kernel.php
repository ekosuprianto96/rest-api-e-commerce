<?php

namespace App\Console;

use Illuminate\Support\Facades\Log;
use App\Jobs\ProsesClearingSaldoToko;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

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
        // $schedule->job(new ProsesClearingSaldoToko)->everyMinute();
        $schedule->command('que:work')->everyMinute();
        Log::info('Cronjob berhasil dijalankan');
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
