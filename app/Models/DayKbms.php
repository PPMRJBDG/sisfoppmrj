<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DayKbms extends Model
{
    protected $fillable = [
        'day_name',
        'is_holiday'
    ];
}
