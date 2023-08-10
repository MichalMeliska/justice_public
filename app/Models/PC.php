<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class PC extends ActiveDirectory
{
    use HasFactory;

    protected $appends = ['created_h', 'install_date_h', 'last_boot_up_time_h', 'online', 'old_specs', 'last_online'];

    public function sanitizeData()
    {
        foreach ($this->data as &$a) {

            $a['Created'] = $this->sanitizeDateTime($a['Created']);

            if (array_key_exists('RegisterLog', $a) and $a['RegisterLog'] > 65535) $a['RegisterLog'] = 65535;

            if (array_key_exists('CanonicalName', $a)) {

                $a['sud'] = $this->sanitizeSud($a['CanonicalName']);

                unset($a['CanonicalName']);

            }

        }
    }

    protected function setTimestamps($timestamps)
    {
        foreach ($this->data as &$row)
            foreach ($timestamps as $column)
                $row[$column] = now();
    }

    protected function InstallDateH(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->dateToHuman($this->InstallDate)
        );
    }

    protected function LastBootUpTimeH(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->dateTimeToHuman($this->LastBootUpTime)
        );
    }

    protected function lastOnline(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->dateTimeToHuman($this->online_at)
        );
    }
}
