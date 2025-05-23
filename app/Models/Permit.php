<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Santri;
use App\Models\Presence;

class Permit extends Model
{
    protected $fillable = [
        'fkSantri_id',
        'fkPresence_id',
        'status',
        'reason',
        'reason_category',
        'approved_by',
        'rejected_by',
        'ids',
        'metadata',
        'status_ss',
        'alasan_rejected',
        'ijin_kuota'
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

    /**
     * Set the keys for a save update query.
     * This is a fix for tables with composite keys
     * TODO: Investigate this later on
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function setKeysForSaveQuery($query)
    {
        $query->where('fkPresence_id', $this->attributes['fkPresence_id']);
        $query->where('fkSantri_id', $this->attributes['fkSantri_id']);

        return $query;
    }
}
