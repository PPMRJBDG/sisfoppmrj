<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sodaqoh extends Model
{
    protected $fillable = [
        'fkSantri_id',
        'periode',
        'nominal',
        'jan',
        'feb',
        'mar',
        'apr',
        'mei',
        'jun',
        'jul',
        'ags',
        'sep',
        'okt',
        'nov',
        'des',
        'jan_date',
        'feb_date',
        'mar_date',
        'apr_date',
        'mei_date',
        'jun_date',
        'jul_date',
        'ags_date',
        'sep_date',
        'okt_date',
        'nov_date',
        'des_date',
        'status_lunas',
        'keterangan',
        'status_rukhso'
    ];

    public function santri()
    {
        return $this->belongsTo(Santri::class, 'fkSantri_id');
    }
}
