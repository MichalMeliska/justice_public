<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Server extends PC
{
    use HasFactory;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->appends[] = 'categ';
    }

    public static function saveExport($file, $timestamp_for, $err_throw = true)
    {
        $instance = new static;

        $instance->data = $instance->csvToArray($file);

        $throw = !in_array('specs_at', $timestamp_for) ? $instance->podatelnaCheck() : false;

        $instance->setTimestamps($timestamp_for);

        $instance->upsert($instance->data, ['SID']);

        if ($err_throw and $throw) throw new \Exception(implode('\r\n', $throw));
    }

    private function podatelnaCheck()
    {
        $throw = [];

        foreach ($this->data as $key => $row) {

            if ($row['offline']) {

                $throw[] = $row['Name'] . ' is offline.';

                unset($this->data[$key]);

            } else {

                if ($row['RegisterRunning'] === 0) $throw[] = $row['Name'] . ' - Register.exe is not running.';
                if ($row['TaskSchedulerRunning'] === 0) $throw[] = $row['Name'] . ' - Scheduled task is not running.';
                if ($row['RegisterLog'] >= 30) $throw[] = $row['Name'] . ' - SOAP is failing.';

                unset($this->data[$key]['offline']);

            }

        }

        return $throw;
    }

    public static function getAll()
    {
        return self::select(
                        'Name AS ComputerName',
                        'servers.*'
                    )
                    ->get()
                    ->mapWithKeys(function ($item) {
                        return [$item['SID'] => $item];
                    });
    }

    protected function online(): Attribute
    {
        return Attribute::make(
            get: fn () => (time() - strtotime($this->online_at)) / 60 < 15
        );
    }

    protected function categ(): Attribute
    {
        return Attribute::make(
            get: function () {

                if (str_starts_with($this->Name, 'SP')) return 'Podateľne';
                elseif (str_starts_with($this->Name, 'SHYP')) return 'Hyp';
                elseif (str_ends_with($this->Name, 'ESS')) return 'Ess';
                elseif (str_starts_with($this->Name, 'SS') or str_starts_with($this->Name, 'STR')) return 'Súborové';
                elseif (str_starts_with($this->Name, 'SD') and !str_starts_with($this->Name, 'SDOCH')) return 'Doménové';
                elseif (str_contains($this->Name, 'DOCH')) return 'Dochádzky';
                elseif (str_contains($this->Name, 'NAS')) return 'Nas';
                else return 'Iné';

            }
        );
    }
}
