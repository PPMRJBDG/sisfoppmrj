<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class MonitoringMateri extends Model
{
    protected $fillable = [
        'fkSantri_id',
        'fkMateri_id',
        'page',
        'status'
    ];

    /**
     * Get the user leader associated with the Lorong.
     */
    public function santri()
    {
        return $this->belongsTo(Santri::class, 'fkSantri_id');
    }

    /**
     * Get the user leader associated with the Lorong.
     */
    public function materi()
    {
        return $this->belongsTo(Materi::class, 'fkMateri_id');
    }
}

