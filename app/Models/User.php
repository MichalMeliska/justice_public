<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;
use PhpOffice\PhpSpreadsheet\IOFactory as Excel;

class User extends ActiveDirectory
{
    use HasFactory;

    protected $appends = ['created_h', 'ms_exch_when_mailbox_created_h', 'password_last_set_h', 'password_expires_h', 'online', 'wrong_pc', 'old_specs'];
    protected $fillable = ['humanet_pass'];
    protected $hidden = ['humanet_pass'];

    public function attendance(): HasMany
    {
        return $this->hasMany(Attendance::class, 'userSID', 'SID');
    }

    public function attendSum(): HasMany
    {
        return $this->hasMany(AttendSum::class, 'userSID', 'SID');
    }

    public function computer(): BelongsTo
    {
        return $this->belongsTo(Computer::class, 'computerSID', 'SID');
    }

    public static function phonebookKSTT($file)
    {
        $xls = Excel::load($file)->getSheet(0)->toArray();

        $oddelenia = ['Sekretariát', 'Informačné centrum', 'Elektronická podateľňa', 'Trestné oddelenie', 'Obchodno-správne oddelenie', 'Pokladňa', 'Civilné oddelenie', 'Odbor informatiky', 'Osobný úrad', 'Pomocné zložky'];

        foreach ($xls as $key => $row) {

            if ($key === 0) continue;

            $col_1[] = array_slice($row, 0, 3);
            $col_2[] = array_slice($row, 4, 3);

        }

        $kanc = null;

        foreach (array_merge($col_1, $col_2) as $row) {

            if ($row[1] === 'Pomocné zložky') break;

            if (in_array($row[1], $oddelenia) or ! $row[1]) continue;

            if ($row[0] and intval($row[0])) $kanc = intval($row[0]);

            if (! $kanc) continue;

            $phonebook[self::stripTitle($row[1])] = $kanc;

        }

        $changes = [];

        foreach (self::getUsersKanc('KSTT') as $meno => $arr)
            if (isset($phonebook[$meno]) and $phonebook[$meno] != $arr['kanc']) $changes[] = [
                'Name' => $arr['fullname'],
                'old' => $arr['kanc'],
                'new' => $phonebook[$meno],
                'SID' => $arr['SID'],
                'sud' => 'KSTT'
            ];

        return $changes;
    }

    public static function phonebookOSTT($file)
    {
        $xls = Excel::load($file)->getSheet(0)->toArray();

        $posch = [' -1. NP', 'Prízemie', '1. poschodie', '2. poschodie', '3. poschodie'];

        foreach ($xls as $row) {

            if (in_array($row[2], $posch) or ! $row[2]) continue;

            if (intval($row[0])) $kanc = $row[0];

            if (! $kanc) continue;

            $arr = explode(',', $row[2]);

            foreach ($arr as $meno) $phonebook[self::stripTitle($meno)] = $kanc;

        }

        $changes = [];

        foreach (self::getUsersKanc('OSTT') as $meno => $arr)
            if (isset($phonebook[$meno]) and $phonebook[$meno] != $arr['kanc']) $changes[] = [
                'Name' => $arr['fullname'],
                'old' => $arr['kanc'],
                'new' => $phonebook[$meno],
                'SID' => $arr['SID'],
                'sud' => 'OSTT'
            ];

        return $changes;
    }

    private static function getUsersKanc($sud)
    {
        return self::select('SID', 'Name', 'kanc')
                    ->where('sud', $sud)
                    ->orderBy('Name')
                    ->get()
                    ->mapWithKeys(fn ($item) => [
                        self::stripTitle($item['Name']) => [
                            'kanc' => $item['kanc'],
                            'fullname' => $item['Name'],
                            'SID' => $item['SID']
                        ]
                    ]);
    }

    public static function stripTitle($name)
    {
        $arr = explode(' ', $name);

        foreach ($arr as $key => &$word)
            if (str_contains($word, '.')) unset($arr[$key]);
            else $word = trim($word);

        return trim(str_replace(',', '', mb_convert_case(implode(' ', $arr), MB_CASE_TITLE, 'UTF-8')));
    }

    public static function savePhonebook($data)
    {
        foreach ($data as &$a) {

            $a['kanc'] = $a['new'];

            unset($a['old'], $a['new']);

        }

        self::upsert($data, ['SID']);
    }

    public function sanitizeData()
    {
        foreach ($this->data as &$a) {

            $a['Created'] = $this->sanitizeDateTime($a['Created']);

            $a['msExchWhenMailboxCreated'] = $this->sanitizeDateTime($a['msExchWhenMailboxCreated']);

            $a['PasswordLastSet'] = $this->sanitizeDateTime($a['PasswordLastSet']);

            $a['PasswordExpires'] = $this->sanitizeDateTime($a['PasswordExpires']);

            $a['Enabled'] = $this->sanitizeBoolean($a['Enabled']);

            $a['LockedOut'] = $this->sanitizeBoolean($a['LockedOut']);

            $a['ipPhone'] = $this->sanitizeNumber($a['ipPhone']);

            $a['sud'] = $this->sanitizeSud($a['CanonicalName']);

            unset($a['CanonicalName']);

        }
    }

    public static function getAll()
    {
        return self::select(
                        'users.SID AS UserSID',
                        'computers.SID AS ComputerSID',
                        'users.Name AS UserName',
                        'computers.Name AS ComputerName',
                        'printers.name AS PrinterName',
                        'printers.ID AS PrinterID',
                        'users.*',
                        'computers.IT',
                        'computers.specs_at',
                        'computers.online_at'
                    )
                    ->leftJoin('computers', 'users.computerSID', '=', 'computers.SID')
                    ->leftJoin('printers', 'computers.printerID', '=', 'printers.ID')
                    ->get()
                    ->mapWithKeys(function ($item) {
                        return [$item['UserSID'] => $item];
                    });
    }

    public static function getUser($sid)
    {
        return self::select(
                        'SID AS UserSID',
                        'Name AS UserName',
                        'users.*'
                    )
                    ->where('SID', $sid)
                    ->first();
    }

    protected function msExchWhenMailboxCreatedH(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->dateToHuman($this->msExchWhenMailboxCreated)
        );
    }

    protected function PasswordLastSetH(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->dateToHuman($this->PasswordLastSet)
        );
    }

    protected function PasswordExpiresH(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->dateToHuman($this->PasswordExpires)
        );
    }

    protected function online(): Attribute
    {
        if (!self::$computer_user_online) self::$computer_user_online = Computer::onlineList();

        return Attribute::make(
            get: fn () => in_array($this->SamAccountName, self::$computer_user_online)
        );
    }

    protected function wrongPc(): Attribute
    {
        if (!self::$computer_user_online) self::$computer_user_online = Computer::onlineList();

        return Attribute::make(
            get: fn () =>   $this->ComputerSID and
                            $this->online and
                            (
                                !array_key_exists($this->ComputerSID, self::$computer_user_online) or
                                self::$computer_user_online[$this->ComputerSID] !== $this->SamAccountName
                            )
        );
    }
}
