<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rabs extends Model
{
    protected $fillable = [
        'periode_tahun',
        'fkDivisi_id',
        'keperluan',
        'periode',
        'jumlah',
        'biaya',
        'bulan_1',
        'bulan_2',
        'bulan_3',
        'bulan_4',
        'bulan_5',
        'bulan_6',
        'bulan_7',
        'bulan_8',
        'bulan_9',
        'bulan_10',
        'bulan_11',
        'bulan_12',
        'is_lock'
    ];

    public function divisi()
    {
        return $this->belongsTo(Divisies::class, 'fkDivisi_id');
    }
}
