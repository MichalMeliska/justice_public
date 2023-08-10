<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Printer extends Model
{
    use HasFactory;

    protected $casts = ['keywords' => 'array'];

    private static $id_keywords;

    public static function getAll()
    {
        return self::select('ID', 'name', 'cartridge')
                    ->get();
    }

    public static function getIdByName($string)
    {
        list($name) = explode(',', strtolower($string));

        if (!self::$id_keywords) self::$id_keywords = self::getKeywords();

        foreach (self::$id_keywords as $id => $keywords)
            foreach ($keywords as $key => $keyword) {

                if (!str_contains($name, $keyword)) continue 2;

                if ($key === array_key_last($keywords)) return $id;

            }

        return null;
    }

    private static function getKeywords()
    {
        return self::select('ID', 'keywords')
                    ->get()
                    ->mapWithKeys(function ($item) {
                        return [$item['ID'] => $item['keywords']];
                    });
    }
}
