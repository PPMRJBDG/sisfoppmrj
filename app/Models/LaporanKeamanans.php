<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Santri;

class LaporanKeamanans extends Model
{
    protected $fillable = [
        'fkSantri_id',
        'jd_kunci_gerbang',
        'jd_kunci_air',
        'jd_kunci_listrik',
        'jd_kunci_lingkungan',
        'jd_kunci_lahan',
        'jd_adzan_malam',
        'jd_nerobos_muadzin',
        'jd_kondisi_umum',
        'event_date',
        'created_at',
        'updated_at',
    ];

    public function santri()
    {
        return $this->belongsTo(Santri::class, 'fkSantri_id');
    }
}