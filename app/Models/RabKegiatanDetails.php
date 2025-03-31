<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RabKegiatanDetails extends Model
{
    protected $fillable = [
        'fkRabKegiatan_id',
        'uraian',
        'qty',
        'satuan',
        'biaya',
        'qty_realisasi',
        'satuan_realisasi',
        'biaya_realisasi',
        'divisi'
    ];

    public function kegiatan()
    {
        return $this->belongsTo(RabKegiatans::class, 'fkRabKegiatan_id');
    }
}
