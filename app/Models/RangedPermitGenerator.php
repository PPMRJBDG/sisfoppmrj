<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class RangedPermitGenerator extends Model
{
    protected $fillable = [
        'fkSantri_id',
        'fkPresenceGroup_id',
        'from_date',
        'to_date',
        'reason',
        'reason_category',
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
    public function presenceGroup()
    {
        return $this->belongsTo(PresenceGroup::class, 'fkPresenceGroup_id');
    }
}
