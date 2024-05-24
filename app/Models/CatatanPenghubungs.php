<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Santri;

class CatatanPenghubungs extends Model
{
    protected $fillable = [
        'fkSantri_id',
        'cat_kepribadian',
        'cat_sholat',
        'cat_kbm',
        'cat_asmara',
        'cat_akhlaq',
        'cat_umum',
        'created_by',
        'status',
        'rating_kepribadian',
        'rating_sholat',
        'rating_kbm',
        'rating_asmara',
        'rating_akhlaq',
        'rating_umum',
    ];

    /**
     * Get the user leader associated with the Lorong.
     */
    public function santri()
    {
        return $this->belongsTo(Santri::class, 'fkSantri_id');
    }
}
