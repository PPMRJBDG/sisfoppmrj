<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evaluations extends Model
{
    protected $fillable = [
        'fkSantri_id',
        'kefahaman',
        'ibadah',
        'akhlaq',
        'takdzim',
        'amalsholih',
        'penampilan',
        'persus',
        'kuliah',
        'ortu',
        'ekonomi',
    ];

    public function santri()
    {
        return $this->belongsTo(Santri::class, 'fkSantri_id');
    }
}
