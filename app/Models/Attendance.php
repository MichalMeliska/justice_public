<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Attendance extends Model
{
    use HasFactory;

    protected $table = 'attendance';
    protected $hidden = ['id', 'userSID'];

    const COOKIE_NAME = 'attendance';
    const SUBMODULE_PATTERN = '/location: (.*?SubModuleChooser.*?)\s/i';

    private $url;
    private $email;
    private $source;
    private $data;
    private $userSID;
    private $year_month;
    private $cookie_file;

    private function getCookieFileName($email)
    {
        $this->cookie_file = Storage::path('humanetCookiesJar/' . substr($email, 0, strpos($email, '@')) . '.txt');
    }

    public static function humanet($email, $password, $year_month = null)
    {
        $instance = new static;

        $instance->email = $email;
        $instance->year_month = $year_month;

        $instance->getCookieFileName($email);

        @unlink($instance->cookie_file);

        $instance->login($password)
            ->startAgenda()
            ->dbLogin()
            ->moduleChooser()
            ->subModuleChooser()
            ->attendLink()
            ->setMonth()
            ->parseMesiac()
            ->parseSumar()
            ->sanitize()
            ->saveData();

        User::findOrFail($instance->userSID)
            ->update(['humanet_pass' => $password]);
        
        return $instance->data;
    }

    private function getSource($post_fields = null, $follow = true)
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $this->url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_PROXY => env('PROXY'),
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HEADER => true,
            CURLOPT_COOKIEJAR => $this->cookie_file,
            CURLOPT_COOKIEFILE => $this->cookie_file,
            CURLOPT_FOLLOWLOCATION => $follow,
            CURLOPT_POST => boolval($post_fields),
            CURLOPT_POSTFIELDS => $post_fields
        ]);

        $this->source = curl_exec($curl);

        if ($this->source === false) $this->getSource($post_fields, $follow);       // occasional random error
    }

    private function fetch($pattern, $multiple = false)
    {
        if ($multiple) return preg_match_all($pattern, $this->source, $match, PREG_SET_ORDER) ? $match : false;
        else return preg_match($pattern, $this->source, $match) ? trim($match[1]) : false;
    }

    private function login($password)
    {
        $this->url = 'https://humanet.sk/moj-humanet/auth/login';

        $this->getSource([
            'email' => $this->email,
            'password' => $password,
            '_do' => 'loginForm-submit'
        ]);

        return $this;
    }

    private function startAgenda()
    {
        if (!$fetch = $this->fetch('/start-agenda-wrapper.*?start-agenda.*?href="(.*?)"/s')) {

            if (!str_contains($this->source, $this->email)) {

                Cookie::queue(Cookie::forget($this::COOKIE_NAME));

                throw new \Exception('Email alebo heslo nie je správne');

            } else throw new \Exception(__METHOD__ . ' - Fetch error');

        }

        $this->url = 'https://humanet.sk' . $fetch;

        $this->getSource(null, false);

        return $this;
    }

    private function dbLogin()
    {
        if (!$fetch = $this->fetch('/<input.*?name="(.*?)" value="(.*?)"/s', true)) throw new \Exception(__METHOD__ . ' - Fetch input error');

        foreach ($fetch as $input) $post_fields[$input[1]] = $input[2];

        if (!$this->url = $this->fetch('/<form.*?action="(.*?)">/')) throw new \Exception(__METHOD__ . ' - Fetch form error');

        $this->getSource($post_fields);

        if (!$this->url = $this->fetch('/<form.*?name="PostForm" action="(.*?)"/')) throw new \Exception(__METHOD__ . ' - Fetch postForm error');

        $this->getSource($post_fields);

        return $this;
    }

    private function moduleChooser()
    {
        $module_pattern = '/location: (.*?ModuleChooser.*?)\s/i';

        if (!$this->url = $this->fetch($module_pattern)) throw new \Exception(__METHOD__ . ' - Fetch module error');

        $this->getSource([
            '__EVENTTARGET' => 'ctl52',
            '__EVENTARGUMENT'	=> 'mod;NALAttendance.dll;Hour.NAL.Attendance.MC.ModulePageAttendance;7;1;ATTENDANCE'
        ]);

        if (!$this->url = $this->fetch($this::SUBMODULE_PATTERN)) {

            if (!$this->url = $this->fetch($module_pattern)) {

                if (!$this->url = $this->fetch('/location: (.*?NewVersion.*?)\s/i')) throw new \Exception(__METHOD__ . ' - Fetch newVersion error');

                $this->getSource(['__EVENTTARGET' => 'btnCancel']);

                return $this->moduleChooser();

            }

            sleep(2);
            return $this->moduleChooser();

        }

        return $this;
    }

    private function subModuleChooser()
    {
        $this->getSource([
            '__EVENTTARGET' => 'SubModul',
            '__EVENTARGUMENT'	=> 'my_attend'
        ]);

        return $this;
    }

    private function attendLink()
    {
        if (!$url = $this->fetch('/location: (.*?RoleIdentifAttendance.*?)\s/i')) {

            if (!$this->fetch($this::SUBMODULE_PATTERN)) throw new \Exception(__METHOD__ . ' - Fetch error');

            sleep(2);
            return $this->subModuleChooser();
        }

        $this->url = $url;

        return $this;
    }

    private function setMonth()
    {
        if (is_null($this->year_month)) {

            if ($this->startingMonth() !== date('Y-m')) {

                $this->nextMonth();

                if (date('j' != 1)) $this->currentMonth();

            } elseif (date('j' != 1)) $this->currentMonth();

        } else {

            list($year, $month) = explode('-', $this->year_month);

            $this->getMonth($year, intval($month));

        }

        return $this;
    }

    private function startingMonth()
    {
        if (!$date = $this->fetch('/name="darE_roleAttendCalendarE\$bcwe_sumItemB\$cmpData\$cmpDtValidAt\$tb" type="text" value="(.*?)"/')) throw new \Exception(__METHOD__ . ' - Fetch error');

        return date('Y-m', strtotime($date));
    }

    private function nextMonth()
    {
        $this->getSource(['__EVENTTARGET' => 'darE_roleAttendCalendarE$ctor_cmpDatePeriodSelector$btnNext']);
    }

    private function currentMonth()
    {
        $this->getSource([
            '__EVENTTARGET' => 'darE_roleAttendCalendarE$bcwe_sumItemB$cmpData$cmpBtnRecalcSummaryItems',
            'darE_roleAttendCalendarE$bcwe_sumItemB$cmpData$cmpDtValidAt$tb' => date('d.m.Y', strtotime('yesterday'))
        ]);
    }

    private function getMonth($year, $month)
    {
        $this->getSource([
            '__EVENTTARGET' => 'darE_roleAttendCalendarE$ctor_cmpDatePeriodSelector$btnSend',
            'darE_roleAttendCalendarE$ctor_cmpDatePeriodSelector$monthNumber$tb' => $month,
            'darE_roleAttendCalendarE$ctor_cmpDatePeriodSelector$yearNumber$tb' => $year
        ]);
    }

    private function parseMesiac()
    {
        if (!$table = $this->fetch('/<table id="darE_roleAttendCalendarE_bcwe_workShifts_cmpData_Tbl".*?>(.*?)<\/table>/s')) throw new \Exception(__METHOD__ . ' - Fetch table error');

        $source = $this->source;
        $this->source = $table;

        if (!$rows = $this->fetch('/<tr.*?<td.*?><a.*?>(.*?)<\/a>.*?<td.*?><a.*?>.*?<\/a>.*?<td.*?><a.*?>.*?<\/a>.*?<td.*?><a.*?>.*?<\/a>.*?<td.*?><a.*?>(.*?)<\/a>.*?<td.*?><a.*?>(.*?)<\/a>.*?<td.*?><a.*?>(.*?)<\/a>.*?<td.*?><a.*?>.*?<\/a>.*?<td.*?><a.*?>(.*?)<\/a>.*?<td.*?><a.*?>.*?<\/a>.*?<td.*?><a.*?>.*?<\/a>.*?<td.*?><a.*?>.*?<\/a>.*?<\/tr>/s', true)) throw new \Exception(__METHOD__ . ' - Fetch rows error');

        foreach ($rows as $arr) {

            foreach ($arr as $key => $value) $arr[$key] = trim(strip_tags($value));

            $this->data['mesiac'][] = [
                'date' => Carbon::createFromFormat('d.m.', substr($arr[1], 0, 6))->toDateString(),
                'volno' => $this->volno($arr[1]) ?: null,
                'trvanie' => $arr[3] ?: null,
                'oddo' => $this->oddo($arr[4], $arr[2]),
                'prerusenie' => $this->prerusenie($arr[5])
            ];

        }

        $this->source = $source;

        return $this;
    }

    private function parseSumar()
    {
        if (!$table = $this->fetch('/<table id="darE_roleAttendCalendarE_bcwe_sumItemB_cmpData_Tbl".*?>(.*?)<\/table>/s')) throw new \Exception(__METHOD__ . ' - Fetch table error');

        $source = $this->source;
        $this->source = $table;

        if (!$rows = $this->fetch('/<tr.*?>.*?<td>(.*?)<\/td><td.*?>(.*?)<\/td>.*?<\/tr>/s', true)) throw new \Exception(__METHOD__ . ' - Fetch rows error');

        foreach ($rows as $arr) {

            foreach ($arr as $key => $value) $arr[$key] = trim(strip_tags(strval($value)));

            $this->data['sumar'][str_replace('akt. ', '', $arr[1])] = is_numeric($arr[2]) ?
                                                                            intval($arr[2]) :
                                                                            (!str_contains($arr[2], ':') ?
                                                                                doubleval($arr[2]) :
                                                                                $arr[2]
                                                                            );

        }

        $this->source = $source;

        return $this;
    }

    private function volno($str)
    {
        return str_contains($str, '+') or str_contains($str, 'so') or str_contains($str, 'ne');
    }

    private function oddo($oddo, $pozn)
    {
        if ($pozn === 'D-cd') return 'Dovolenka';
        if ($pozn === 'LRP-cd') return 'Lekár sprievod';
        if ($pozn === 'LZAM-cd') return 'Lekár';
        if ($pozn === 'VoľnoKZ') return 'Sick day';
        if ($pozn === 'HO') return 'Home office';
        return $oddo ?: null;
    }

    private function prerusenie($str)
    {
        if ($str === 'LZAM-i') return 'Lekár';
        if ($str === 'LRP-i') return 'Lekár sprievod';

        return $str ?: null;
    }

    private function sanitize()
    {
        $this->userSID = User::select('SID')
                            ->where('EmailAddress', $this->email)
                            ->firstOrFail()
                            ->SID;

        foreach (array_keys($this->data['mesiac']) as $key) $this->data['mesiac'][$key]['userSID'] = $this->userSID;

        $this->data['sumar']['completed'] = ($this->year_month and date('j') > 5) ? true : null;

        return $this;
    }

    private function saveData()
    {
        DB::transaction(function () {

            $this->where('userSID', $this->userSID)
                ->whereRaw("DATE_FORMAT(date, '%Y-%m') = ?", [$this->year_month ?: date('Y-m')])
                ->delete();

            $this->insert($this->data['mesiac']);

            AttendSum::updateOrCreate([
                    'userSID' => $this->userSID,
                    'month' => ($this->year_month ?: date('Y-m'))
                ], [...$this->data['sumar']]
            );

        });
    }

    public static function nextPrevMonth($current, $change)
    {
        $cur_month = Carbon::parse($current);

        return $change === 1 ? $cur_month->addMonth()->format('Y-m') : $cur_month->subMonth()->format('Y-m');
    }

    protected function date(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => Carbon::parse($value)->format('d.m. ') . mb_substr(Carbon::parse($value)->dayName, 0, 2)
        );
    }
}
