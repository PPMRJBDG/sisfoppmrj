<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PmbKonfigurasis extends Model
{
    protected $fillable = [
        'tahun_pmb',
        'gelombang1',
        'gelombang2',
        'informasi_pmb',
    ];
}
