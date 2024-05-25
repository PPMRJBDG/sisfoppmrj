<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HourKbms extends Model
{
    protected $fillable = [
        'hour_name',
        'is_break',
        'is_disable'
    ];
}
