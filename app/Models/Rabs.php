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
        'is_lock',
        'create_rab'
    ];

    public function divisi()
    {
        return $this->belongsTo(Divisies::class, 'fkDivisi_id');
    }

    public function totalRealisasi($id_rab)
    {
        $jurnals = Jurnals::where('fkRab_id', $id_rab)->get();
        $total = 0;
        if($jurnals){
            foreach($jurnals as $j){
                $total += $j->qty * $j->nominal;
            }
        }
        return $total;
    }
}
