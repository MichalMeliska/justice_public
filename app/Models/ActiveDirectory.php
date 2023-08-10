<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class ActiveDirectory extends Model
{
    use HasFactory;

    protected $primaryKey = 'SID';
    protected $keyType = 'string';
    public $timestamps = false;

    protected $data;
    protected static $computer_user_online;

    public static function saveADExport($file, $sid = null)
    {
        $instance = new static;

        $instance->data = $instance->csvToArray($file);

        $instance->sanitizeData();

        if (is_null($sid)) $instance->removeUnexistent();

        $instance->upsert($instance->data, ['SID']);
    }

    public static function csvToArray($file)
    {
        $file_arr = file($file);

        if (count($file_arr) < 2) throw new \Exception('Prázdny import.');

        $array = array_map('str_getcsv', $file_arr);

        foreach ($array[0] as $header) $keys[] = preg_replace('/[^A-Za-z0-9\-\_]/', '', $header);

        array_walk($array, function(&$a) use ($keys) {

            $a = array_combine($keys, $a);

            foreach ($a as &$b)
                if ($b === '')
                    $b = null;

        });

        array_shift($array);

        return $array;
    }

    private function removeUnexistent()
    {
        $delete_SIDs = array_diff($this->getSIDs(), array_column($this->data, 'SID'));

        if ($delete_SIDs) {

            if ((new \ReflectionClass(get_called_class()))->getShortName() === 'Computer') User::whereIn('computerSID', $delete_SIDs)
                                                                                                ->update(['computerSID' => null]);

            $this->whereIn('SID', $delete_SIDs)
                ->forceDelete();

        }
    }

    private function getSIDs()
    {
        return $this->select('SID')
                    ->get()
                    ->modelKeys();
    }

    protected function sanitizeSud($value)
    {
        $value = str_replace('-', '', $value);

        $arr = explode('/', $value);

        return in_array($arr[2], ['Users', 'PC']) ? 'KSTT' : $arr[2];
    }

    protected function sanitizeDateTime($date)
    {
        return $date ?: null;
    }

    protected function sanitizeBoolean($string)
    {
        return $string === 'True' ? true : false;
    }

    protected function sanitizeNumber($string)
    {
        return $string ? intval(str_replace(' ', '', $string)) : null;
    }

    protected function dateTimeToHuman($mysql_date)
    {
        return is_null($mysql_date) ? null : date('d.m.Y H:i', strtotime($mysql_date));
    }

    protected function dateToHuman($mysql_date)
    {
        return is_null($mysql_date) ? null : date('d.m.Y', strtotime($mysql_date));
    }

    protected function createdH(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->dateToHuman($this->Created)
        );
    }

    protected function oldSpecs(): Attribute
    {
        return Attribute::make(
            get: fn () =>   $this->specs_at and
                            substr($this->specs_at, 0, 10) !== date('Y-m-d') and
                            substr($this->online_at, 0, 10) === date('Y-m-d')
        );
    }
}
