<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\DewanPengajars;

class KalenderPpmTemplates extends Model
{
    protected $fillable = [
        'waktu',
        'kelas',
        'sequence',
        'fkDewanPengajar_id',
        'is_agenda_khusus',
        'nama_agenda_khusus',
        'day',
        'requires_presence'
    ];

    public function pengajar()
    {
        return $this->belongsTo(DewanPengajars::class, 'fkDewanPengajar_id');
    }

    public function data_checkbox($seq,$kbm){

    }
}
