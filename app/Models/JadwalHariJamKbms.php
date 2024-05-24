<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JadwalHariJamKbms extends Model
{
    protected $fillable = [
        'fkSantri_id',
        'fkHari_kbm_id',
        'fkJam_kbm_id'
    ];
}
