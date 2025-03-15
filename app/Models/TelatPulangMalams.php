<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Santri;

class TelatPulangMalams extends Model
{
    protected $fillable = [
        'fkJaga_malam_id',
        'fkSantri_id',
        'jam_pulang',
        'alasan',
    ];

    public function santri()
    {
        return $this->belongsTo(Santri::class, 'fkSantri_id');
    }

    public function jaga()
    {
        return $this->belongsTo(Santri::class, 'fkJaga_malam_id');
    }
}