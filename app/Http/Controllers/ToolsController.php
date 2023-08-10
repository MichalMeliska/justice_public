<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Powershell;
use App\Models\User;
use App\Models\Server;
use App\Models\Computer;

class ToolsController extends Controller
{
    public function rdp($hostname, $route)
    {
        $admin = $route === 'Servers' ? 1 : 0;

        $fn = fn() => Powershell::rdp($hostname, $admin);

        return $this->response($fn);
    }

    public function dameware($hostname)
    {
        $fn = fn() => Powershell::dameware($hostname);

        return $this->response($fn);
    }

    public function folder($hostname, $route)
    {
        $admin = $route === 'Servers' ? 1 : 0;

        $fn = fn() => Powershell::folder($hostname, $admin);

        return $this->response($fn);
    }

    public function email($address)
    {
        $fn = fn() => Powershell::email($address);

        return $this->response($fn);
    }

    public function registerCopy($ComputerSID)
    {
        $fn = fn() => Powershell::registerCopy($ComputerSID);

        return $this->response($fn);
    }

    public function refresh($route, $sid)
    {
        $fn = function () use($route, $sid) {

            if ($route === 'Servers') {

                Powershell::refreshServer($sid);

                return Server::findOrFail($sid);

            } elseif ($route === 'Users') {

                Powershell::refreshUser($sid);

                return User::getUser($sid);

            } elseif ($route === 'Computers') {

                Powershell::refreshComputer($sid);

                $computer = Computer::getComputer($sid);

                return [
                    'computer' => $computer,
                    'users' => array_map(function($el) {
                            return $el['SID'];
                        }, $computer->users->toArray())
                ];

            }

        };

        return $this->response($fn);
    }

    public function wol($mac)
    {
        $fn = fn() => Powershell::wol($mac);

        return $this->response($fn);
    }

    public function getInstalledPrinters($hostname)
    {
        $fn = fn() => Powershell::getInstalledPrinters($hostname);

        return $this->response($fn);
    }

    public function setDeafultPrinter(Request $request)
    {
        $fn = fn() => Powershell::setDeafultPrinter($request->UserSID, $request->printer);

        return $this->response($fn);
    }

    public function assignUserComputer(Request $request)
    {
        $fn = function () use ($request) {

            $user = User::findOrFail($request->userSID);

            $user->computerSID = $request->computerSID;

            $user->save();

            $computer = $user->computer;
            $printer = $user->computer->printer;

            return [
                'UserSID' => $user->SID,
                'ComputerName' => $computer->Name,
                'ComputerSID' => $user->computerSID,
                'PrinterID' => $printer->ID ?? null,
                'PrinterName' => $printer->name ?? null
            ];

        };

        return $this->response($fn);
    }

    public function registerRestart($podatelna)
    {
        $fn = fn() => Powershell::registerRestart($podatelna);

        return $this->response($fn);
    }
}
