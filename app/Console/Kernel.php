<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use App\Mail\ScheduleTaskError;
use App\Models\Computer;
use App\Models\User;
use App\Models\Server;
use App\Models\Powershell;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $this->ADComputers($schedule);
        $this->ADUsers($schedule);
        $this->ADServers($schedule);
        $this->ServerSpecs($schedule);
        $this->ServerRunning($schedule);
        $this->PCSpecs($schedule);
        $this->PCOnline($schedule);
        $this->housekeeping($schedule);
    }

    private function housekeeping($schedule)
    {
        $schedule->call(function() {

                foreach (Storage::files(Powershell::EXPORT_PATH) as $filename)
                    if (str_starts_with($filename, Powershell::EXPORT_PATH . 'SID_'))
                        Storage::delete($filename);

        })
            ->weekdays()
            ->daily();
    }

    private function ADComputers($schedule)
    {
        $filename = 'ADComputers';

        $schedule->exec(Powershell::exportCmd($filename))
            ->weekdays()
            ->dailyAt('06:00')
            ->emailOutputOnFailure(config('const.OWNER_MAIL_ADDRESS'))
            ->onSuccess(function () use ($filename) {

                try {
                    Computer::saveADExport(Powershell::exportPath($filename));
                } catch (\Exception $e) {
                    Mail::to(config('const.OWNER_MAIL_ADDRESS'))->send(new ScheduleTaskError($filename, $e->getMessage()));
                }

            });
    }

    private function ADUsers($schedule)
    {
        $filename = 'ADUsers';

        $schedule->exec(Powershell::exportCmd($filename))
            ->weekdays()
            ->dailyAt('06:00')
            ->emailOutputOnFailure(config('const.OWNER_MAIL_ADDRESS'))
            ->onSuccess(function () use ($filename) {

                try {
                    User::saveADExport(Powershell::exportPath($filename));
                } catch (\Exception $e) {
                    Mail::to(config('const.OWNER_MAIL_ADDRESS'))->send(new ScheduleTaskError($filename, $e->getMessage()));
                }

            });
    }

    private function ADServers($schedule)
    {
        $filename = 'ADServers';

        $schedule->exec(Powershell::exportCmd($filename))
            ->weekdays()
            ->dailyAt('06:00')
            ->emailOutputOnFailure(config('const.OWNER_MAIL_ADDRESS'))
            ->onSuccess(function () use ($filename) {

                try {
                    Server::saveADExport(Powershell::exportPath($filename));
                } catch (\Exception $e) {
                    Mail::to(config('const.OWNER_MAIL_ADDRESS'))->send(new ScheduleTaskError($filename, $e->getMessage()));
                }

            });
    }

    private function ServerSpecs($schedule)
    {
        $filename = 'ServerSpecs';

        $schedule->exec(Powershell::exportCmd($filename, true))
            ->weekdays()
            ->dailyAt('06:00')
            ->emailOutputOnFailure(config('const.OWNER_MAIL_ADDRESS'))
            ->onSuccess(function () use ($filename) {

                try {
                    Server::saveExport(Powershell::exportPath($filename), ['specs_at', 'online_at']);
                } catch (\Exception $e) {
                    Mail::to(config('const.OWNER_MAIL_ADDRESS'))->send(new ScheduleTaskError($filename, $e->getMessage()));
                }

            });
    }

    private function ServerRunning($schedule)
    {
        $filename = 'ServerRunning';

        $schedule->exec(Powershell::exportCmd($filename, true))
            ->weekdays()
            ->everyTenMinutes()
            ->between('6:10', '18:00')
            ->emailOutputOnFailure(config('const.OWNER_MAIL_ADDRESS'))
            ->onSuccess(function () use ($filename) {

                try {
                    Server::saveExport(Powershell::exportPath($filename), ['online_at']);
                } catch (\Exception $e) {
                    Mail::to(config('const.OWNER_MAIL_ADDRESS'))->send(new ScheduleTaskError($filename, $e->getMessage()));
                }

            });
    }

    private function PCSpecs($schedule)
    {
        $filename = 'PCSpecs';

        $schedule->exec(Powershell::exportCmd($filename, true))
            ->weekdays()
            ->hourly()
            ->between('7:00', '10:01')
            ->emailOutputOnFailure(config('const.OWNER_MAIL_ADDRESS'))
            ->onSuccess(function () use ($filename) {

                try {
                    Computer::saveExport(Powershell::exportPath($filename), ['specs_at', 'online_at']);
                } catch (\Exception $e) {
                    Mail::to(config('const.OWNER_MAIL_ADDRESS'))->send(new ScheduleTaskError($filename, $e->getMessage()));
                }

            });
    }

    private function PCOnline($schedule)
    {
        $filename = 'PCOnline';

        $schedule->exec(Powershell::exportCmd($filename, true))
            ->weekdays()
            ->everyTenMinutes()
            ->between('6:00', '18:00')
            ->emailOutputOnFailure(config('const.OWNER_MAIL_ADDRESS'))
            ->onSuccess(function () use ($filename) {

                try {
                    Computer::saveExport(Powershell::exportPath($filename), ['online_at']);
                } catch (\Exception $e) {
                    Mail::to(config('const.OWNER_MAIL_ADDRESS'))->send(new ScheduleTaskError($filename, $e->getMessage()));
                }

            });
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
