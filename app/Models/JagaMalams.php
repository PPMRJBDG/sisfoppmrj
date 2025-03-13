<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JagaMalams extends Model
{
    protected $fillable = [
        'ppm',
        'putaran_ke',
        'anggota',
        'status',
    ];
}
