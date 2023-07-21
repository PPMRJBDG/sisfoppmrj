<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Santri;
use App\Models\Presence;

class Present extends Model
{
    protected $fillable = [
        'fkSantri_id',
        'fkPresence_id',
        'is_late'
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
    public function presence()
    {
        return $this->belongsTo(Presence::class, 'fkPresence_id');
    }
}

