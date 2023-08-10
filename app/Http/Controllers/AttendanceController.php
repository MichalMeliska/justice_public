<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Computer;
use App\Models\User;

class AttendanceController extends Controller
{
    private $user;

    public function attendance(Request $request)
    {
        if (Cookie::has(Attendance::COOKIE_NAME)) $fn = function () use ($request) {

            $this->getUser();

            if ($request->change_month) {

                $request->change_month = Attendance::nextPrevMonth($request->cur_month, $request->change_month);

                if ($request->change_month === date('Y-m')) $request->change_month = false;

            }

            if (
                $request->force or
                (
                    $request->change_month and
                    $this->user
                        ->attendSum()
                        ->where('month', $request->change_month)
                        ->where(function (Builder $query) {
                            $query->where('completed', 1)
                                ->orWhereRaw('DATE(updated_at) = CURDATE()');
                        })
                        ->doesntExist()
                ) or
                (
                    !$request->change_month and
                    $this->user
                        ->attendSum()
                        ->where('month', date('Y-m'))
                        ->whereRaw('DATE(updated_at) = CURDATE()')
                        ->doesntExist()
                )
            ) Attendance::humanet(Cookie::get(Attendance::COOKIE_NAME), $this->user->humanet_pass, $request->change_month ?? null);

            return $this->getAll($request->change_month ?? null);

        };
        else {

            if ($request->mail) $fn = function () use ($request) {

                if ($this->getUser($request->mail)
                        ->attendSum()
                        ->where('month', date('Y-m'))
                        ->whereRaw('DATE(updated_at) = CURDATE()')
                        ->doesntExist()
                ) Attendance::humanet($request->mail, $request->password);

                if ($request->remember) Cookie::queue(Attendance::COOKIE_NAME, $request->mail, 60*24*365);

                return $this->getAll();

            };
            else $fn = fn() => ['email' => Computer::getCurrentUserEmail()];

        }

        return $this->response($fn);
    }

    private function getUser($email = null)
    {
        $this->user = User::select('SID', 'humanet_pass')->where('EmailAddress', $email ?: Cookie::get(Attendance::COOKIE_NAME))->firstOrFail();

        return $this->user;
    }

    private function getAll($month = null)
    {
        return [
            'attendance' => [
                'mesiac' => $this->user->attendance()->whereRaw("DATE_FORMAT(date, '%Y-%m') = ?", [$month ?: date('Y-m')])->get(),
                'sumar' => $this->user->attendSum()->where('month', $month ?: date('Y-m'))->firstOrFail()
            ]
        ];
    }

    public function logout()
    {
        Cookie::queue(Cookie::forget(Attendance::COOKIE_NAME));
    }
}
