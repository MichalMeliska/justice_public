<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;

class AttendSum extends Attendance
{
    use HasFactory;

    protected $table = 'attend_sum';
    protected $guarded = [];
    protected $appends = ['month_h', 'next', 'prev'];

    const CREATED_AT = null;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->hidden = array_merge($this->hidden, ['completed', 'updated_at']);
    }

    protected function monthH(): Attribute
    {
        return Attribute::make(
            get: fn () => ucfirst(Carbon::parse($this->month)->translatedFormat('F Y'))
        );
    }

    protected function next(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->month !== date('Y-m')
        );
    }

    protected function prev(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->month !== '2020-12'
        );
    }
}
