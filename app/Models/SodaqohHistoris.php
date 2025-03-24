<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Santri;
use App\Models\Sodaqoh;

class SodaqohHistoris extends Model
{
    protected $fillable = [
        'fkSodaqoh_id',
        'fkSantri_id',
        'nominal',
        'bukti_transfer',
        'status',
        'updated_by',
        'pay_date',
    ];

    public function santri()
    {
        return $this->belongsTo(Santri::class, 'fkSantri_id');
    }

    public function sodaqoh()
    {
        return $this->belongsTo(Sodaqoh::class, 'fkSodaqoh_id');
    }
}
