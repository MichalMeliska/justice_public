<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Computer extends PC
{
    use HasFactory;

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'computerSID', 'SID');
    }

    public function printer(): BelongsTo
    {
        return $this->belongsTo(Printer::class, 'printerID', 'ID');
    }

    public static function saveExport($file, $timestamp_for)
    {
        $instance = new static;

        $instance->data = $instance->csvToArray($file);

        if (in_array('specs_at', $timestamp_for)) $instance->setPrinterId();

        $instance->setTimestamps($timestamp_for);

        $instance->upsert($instance->data, ['SID']);
    }

    private function setPrinterId()
    {
        foreach ($this->data as &$row) {

            $row['printerID'] = $row['Printer'] ? Printer::getIdByName($row['Printer']) : null;

            unset($row['Printer']);

        }
    }

    public static function onlineList()
    {
        return self::select('SID', 'loggedUser')
                    ->whereRaw('online_at > NOW() - INTERVAL 15 MINUTE')
                    ->get()
                    ->mapWithKeys(function ($item) {
                        return [$item['SID'] => $item['loggedUser']];
                    })
                    ->toArray();
    }

    public static function getAll()
    {
        return self::select(
                        'users.SID AS UserSID',
                        'computers.SID AS ComputerSID',
                        'users.Name AS UserName',
                        'computers.Name AS ComputerName',
                        'computers.*'
                    )
                    ->leftJoin('users', 'users.SamAccountName', '=', 'computers.LoggedUser')
                    ->get()
                    ->mapWithKeys(function ($item) {
                        return [$item['ComputerSID'] => $item];
                    });
    }

    public static function getComputer($sid)
    {
        return self::select(
                        'computers.SID',                                    // kvoli hasMany
                        'computers.SID as ComputerSID',                     // kvoli online attributu
                        'computers.Name AS ComputerName',
                        'printers.name AS PrinterName',
                        'printers.ID AS PrinterID',
                        'computers.*'
                    )
                    ->where('SID', $sid)
                    ->leftJoin('printers', 'printers.ID', '=', 'computers.printerID')
                    ->firstOrFail();
    }

    public static function getCurrentUserEmail()
    {
        $comp = self::select('SID')
                    ->where('computers.Name', strtoupper(gethostname()))
                    ->first();

        return ($comp and $comp->users()->exists()) ? $comp->users[0]->EmailAddress : null;
    }

    public static function computerUserPairing()
    {
        $computers = self::select('Name', 'SID')->get()->mapWithKeys(function ($item) {
            return [$item['SID'] => strtolower($item['Name'])];
        })->all();

        $users = User::select('Name', 'sud', 'SID')->get()->mapWithKeys(function ($item) {
            return [$item['SID'] => strtolower($item['sud']) . str_replace(['á','é','í','ý','ó','ú','ľ','š','č','ť','ž','ň','ď','ö'], ['a','e','i','y','o','u','l','s','c','t','z','n','d','o'], mb_strtolower(substr($item['Name'], 0, strpos($item['Name'], ' '))))];
        })->all();

        $count = array_count_values($users);

        foreach ($computers as $sid => $pcname)
            if (isset($count[$pcname]) and $count[$pcname] === 1)
                User::where('SID', array_search($pcname, $users))->update(['computerSID' => $sid]);

    }

    protected function online(): Attribute
    {
        if (!self::$computer_user_online) self::$computer_user_online = self::onlineList();

        return Attribute::make(
            get: fn () => array_key_exists($this->ComputerSID, self::$computer_user_online)
        );
    }
}
