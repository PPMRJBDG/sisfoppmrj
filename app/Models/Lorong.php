<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Lorong extends Model
{
    protected $fillable = [
        'name',
        'fkSantri_leaderId'
    ];

    /**
     * Get the user leader associated with the Lorong.
     */
    public function leader()
    {
        return $this->belongsTo(Santri::class, 'fkSantri_leaderId');
    }

    /**
     * Get the user leader associated with the Lorong.
     */
    public function members()
    {
        return $this->hasMany(Santri::class, 'fkLorong_id');
    }
}

