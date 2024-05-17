<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\PresenceGroup;
use App\Models\DewanPengajars;

class JadwalPengajars extends Model
{
    protected $fillable = [
        'ppm',
        'fkPresence_group_id',
        'fkDewan_pengajar_id',
        'day',
    ];

    public function presenceGroup()
    {
        return $this->belongsTo(PresenceGroup::class, 'fkPresence_group_id');
    }

    public function pengajar()
    {
        return $this->belongsTo(DewanPengajars::class, 'fkDewan_pengajar_id');
    }
}
