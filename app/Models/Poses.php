<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Tingkatans;

class Poses extends Model
{
    protected $fillable = [
        'name',
        'fkTingkatan_id',
    ];

    public function tingkatan()
    {
        return $this->belongsTo(Tingkatans::class, 'fkTingkatan_id');
    }
}
