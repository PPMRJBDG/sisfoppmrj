<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rabs extends Model
{
    protected $fillable = [
        'periode_tahunan',
        'fkDivisi_id',
        'fkRab_periode_id',
        'jumlah',
        'biaya',
        'bulan_jan',
        'bulan_feb',
        'bulan_mar',
        'bulan_apr',
        'bulan_mei',
        'bulan_juni',
        'bulan_juli',
        'bulan_ags',
        'bulan_sept',
        'bulan_okt',
        'bulan_nov',
        'bulan_des'
    ];

    public function divisi()
    {
        return $this->belongsTo(Divisis::class, 'fkDivisi_id');
    }

    public function rab_periode()
    {
        return $this->belongsTo(RabPeriodes::class, 'fkRab_periode_id');
    }
}
