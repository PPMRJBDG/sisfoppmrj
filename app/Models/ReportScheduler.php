<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Santri;

class ReportScheduler extends Model
{
    protected $fillable = [
        'fkSantri_id',
        'link_url',
        'month',
        'status',
        'scheduler',
        'created_at',
        'updated_at',
        'ids'
    ];

    public function santri()
    {
        return $this->belongsTo(Santri::class, 'fkSantri_id');
    }
}
