<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Santri;

class PanitiaPmbs extends Model
{
    protected $fillable = [
        'fkSantri_id',
    ];

    /**
     * Get the user leader associated with the Lorong.
     */
    public function santri()
    {
        return $this->belongsTo(Santri::class, 'fkSantri_id');
    }
}
