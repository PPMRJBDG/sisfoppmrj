<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Santri;
use App\Models\SodaqohHistoris;

class Sodaqoh extends Model
{
    protected $fillable = [
        'fkSantri_id',
        'periode',
        'nominal',
        'status_lunas',
        'keterangan',
        'status_rukhso'
    ];

    public function santri()
    {
        return $this->belongsTo(Santri::class, 'fkSantri_id');
    }

    public function histori()
    {
        return $this->hasMany(SodaqohHistoris::class, 'fkSodaqoh_id');
    }
}
