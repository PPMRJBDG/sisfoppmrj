<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KalenderPpms extends Model
{
    protected $fillable = [
        'x',
        'bulan',
        'start',
        'is_certain_conditions',
        'waktu_certain_conditions',
        'nama_certain_conditions'
    ];
}
