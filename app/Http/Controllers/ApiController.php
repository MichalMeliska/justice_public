<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Computer;
use App\Models\Server;
use App\Models\Printer;
use App\Models\Schedule;
use App\Models\Powershell;
use App\Models\Ip;

class ApiController extends Controller
{
    public function getRefreshData()
    {
        $fn = fn() => [
            'users' => User::getAll(),
            'computers' => Computer::getAll(),
            'servers' => Server::getAll()
        ];

        return $this->response($fn);
    }

    public function getRegisterVersionServer()
    {
        $fn = fn() => Server::max('RegisterVersion');

        return $this->response($fn);
    }

    public function getPrinters()
    {
        $fn = fn() => Printer::getAll();

        return $this->response($fn);
    }

    public function phonebookImport(Request $request)
    {
        if ($request->sud === 'KSTT') $fn = fn() => User::phonebookKSTT($request->file);
        elseif ($request->sud === 'OSTT') $fn = fn() => User::phonebookOSTT($request->file);
        else $fn = fn() => throw new \Exception('Neznámy súd');

        return $this->response($fn);
    }

    public function phonebookChanges(Request $request)
    {
        $fn = function () use ($request) {

            User::savePhonebook($request->data);

            return User::getAll();

        };

        return $this->response($fn);
    }

    public function scheduleImport(Request $request)
    {
        $fn = fn() => Schedule::compare($request->rozpis, $request->export);

        return $this->response($fn);
    }

    public function getIP()
    {
        $fn = fn() => Ip::all();

        return $this->response($fn);
    }
}
