<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;
use Illuminate\Process\Pool;

class Powershell extends Model
{
    use HasFactory;

    const PWSH = '"C:\Program Files\PowerShell\7\pwsh.exe" ';
    const SCRIPT_PATH = 'powershell/script/';
    const EXPORT_PATH = 'powershell/export/';

    public static function rdp($hostname, $admin)
    {
        $process = Process::run(
            'powershell ' . Storage::path(self::SCRIPT_PATH . 'Rdp.ps1') .
            ' -hostname ' . escapeshellarg($hostname) .
            ' -admin ' . $admin
        );

        if ($process->failed()) throw new \Exception($process->output());
    }

    public static function dameware($hostname)
    {
        /*$process = Process::start(
            'powershell ' . Storage::path(self::SCRIPT_PATH . 'Dameware.ps1 -hostname ' . escapeshellarg($hostname))
        )->wait();

        if ($process->failed()) throw new \Exception($process->output());*/

        pclose(popen('start /B powershell ' . Storage::path(self::SCRIPT_PATH . 'Dameware.ps1') .
            ' -hostname ' . escapeshellarg($hostname), 'r')
        );

        sleep(5);
    }

    public static function folder($hostname, $admin)
    {
        $process = Process::run(
            'powershell ' . Storage::path(self::SCRIPT_PATH . 'Folder.ps1') .
            ' -hostname ' . escapeshellarg($hostname) .
            ' -admin ' . $admin
        );

        if ($process->failed()) throw new \Exception($process->output());
    }

    public static function email($address)
    {
        $process = Process::run(
            'powershell ' . Storage::path(self::SCRIPT_PATH . 'Email.ps1') .
            ' -address ' . escapeshellarg($address)
        );

        if ($process->failed()) throw new \Exception($process->output());
    }

    public static function registerCopy($ComputerSID)
    {
        $computer = Computer::findOrFail($ComputerSID);

        $process = Process::run(
            'powershell ' . Storage::path(self::SCRIPT_PATH . 'RegisterCopy.ps1') .
            ' -hostname ' . escapeshellarg($computer->Name)
        );

        if ($process->failed()) throw new \Exception($process->output());

        $computer->RegisterVersion = Server::max('RegisterVersion');

        $computer->save();
    }

    public static function exportCmd($filename, $pwsh = false, $sid = false)
    {
        $ps = $pwsh ? self::PWSH : 'powershell ';

        return $ps . Storage::path(self::SCRIPT_PATH . $filename . '.ps1') .
            ($sid ? ' -sid ' . $sid : null) .
            ' -export ' . self::exportPath($filename, $sid);
    }

    public static function exportPath($filename, $sid = false)
    {
        $sid = $sid ? 'SID_' . $sid . '_' : '';

        return Storage::path(self::EXPORT_PATH . $sid . $filename . '.csv');
    }

    public static function refreshComputer($sid)
    {
        $filenameAD = 'ADComputers';
        $filenameSpecs = 'PCSpecs';

        [$a, $b] = Process::pool(function (Pool $pool) use ($filenameAD, $filenameSpecs, $sid) {

            $pool->command(self::exportCmd($filenameAD, false, $sid));
            $pool->command(self::exportCmd($filenameSpecs, false, $sid));

        })->run();

        foreach([$a, $b] as $result)
            if ($result->failed())
                throw new \Exception($result->output());

        Computer::saveADExport(self::exportPath($filenameAD, $sid), $sid);
        Computer::saveExport(self::exportPath($filenameSpecs, $sid), ['specs_at', 'online_at']);
    }

    public static function refreshServer($sid)
    {
        $filenameAD = 'ADServers';
        $filenameSpecs = 'ServerSpecs';
        $filenameRunning = 'ServerRunning';

        [$a, $b, $c] = Process::pool(function (Pool $pool) use ($filenameAD, $filenameSpecs, $filenameRunning, $sid) {

            $pool->command(self::exportCmd($filenameAD, false, $sid));
            $pool->command(self::exportCmd($filenameSpecs, false, $sid));
            $pool->command(self::exportCmd($filenameRunning, false, $sid));

        })->run();

        foreach([$a, $b, $c] as $result)
            if ($result->failed())
                throw new \Exception($result->output());

        Server::saveADExport(self::exportPath($filenameAD, $sid), $sid);
        Server::saveExport(self::exportPath($filenameSpecs, $sid), ['specs_at']);
        Server::saveExport(self::exportPath($filenameRunning, $sid), ['online_at'], false);
    }

    public static function refreshUser($sid)
    {
        $filename = 'ADUsers';

        $process = Process::run(self::exportCmd($filename, false, $sid));

        if ($process->failed()) throw new \Exception($process->output());

        User::saveADExport(self::exportPath($filename, $sid), $sid);
    }

    public static function wol($mac)
    {
        $process = Process::run(
            'powershell ' . Storage::path(self::SCRIPT_PATH . 'Wol.ps1') .
            ' -mac ' . escapeshellarg($mac)
        );

        if ($process->failed()) throw new \Exception($process->output());
    }

    public static function getInstalledPrinters($hostname)
    {
        $filename = 'getInstalledPrinters';

        $cmd = Powershell::exportCmd($filename) . ' -hostname ' . escapeshellarg($hostname);

        $process = Process::run($cmd);

        if ($process->failed()) throw new \Exception($process->output());

        $printers = array_column(ActiveDirectory::csvToArray(self::exportPath($filename)), 'Name');

        return $printers;
    }

    public static function setDeafultPrinter($UserSID, $printer)
    {
        $computer = User::findOrFail($UserSID)->computer;

        $process = Process::run(
            'powershell ' . Storage::path(self::SCRIPT_PATH . 'setDefaultPrinter.ps1') .
            ' -sid ' . $UserSID .
            ' -hostname ' . escapeshellarg($computer->Name) .
            ' -printerName \'' . escapeshellarg($printer) . '\''
        );

        if ($process->failed()) throw new \Exception($process->output());

        $computer->printerID = Printer::getIdByName($printer);

        $computer->save();

        return [
            'printerID' => $computer->printerID,
            'printerName' => $computer->printerID ? Printer::find($computer->printerID)->name : null
        ];
    }

    public static function registerRestart($podatelna)
    {
        $process = Process::run(
            'powershell ' . Storage::path(self::SCRIPT_PATH . 'RegisterRestart.ps1') .
            ' -podatelna ' . escapeshellarg($podatelna)
        );

        if ($process->failed()) throw new \Exception($process->output());

        Server::where('Name', $podatelna)->update([
            'RegisterRunning' => 1,
            'TaskSchedulerRunning' => 1,
            'RegisterLog' => 0
        ]);
    }
}
