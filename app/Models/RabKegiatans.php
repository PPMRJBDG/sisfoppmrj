<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RabKegiatans extends Model
{
    protected $fillable = [
        'fkRab_id',
        'nama',
        'periode_bulan',
        'status',
        'deskripsi',
        'fkSantri_id_ketua',
        'fkSantri_id_bendahara',
        'ids',
        'justifikasi_rab',
        'justifikasi_realisasi',
    ];

    public function rab()
    {
        return $this->belongsTo(Rabs::class, 'fkRab_id');
    }

    public function details()
    {
        return $this->hasMany(RabKegiatanDetails::class, 'fkRabKegiatan_id');
    }

    public function total_biaya()
    {
        $details = RabKegiatanDetails::where('fkRabKegiatan_id',$this->id)->get();
        $total_biaya = 0;
        if($details!=null){
            foreach($details as $d){
                $total_biaya = $total_biaya + ($d->qty_realisasi*$d->biaya_realisasi);
            }
        }
        return $total_biaya;
    }
}
