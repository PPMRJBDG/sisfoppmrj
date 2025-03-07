<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Tingkatans;

class FsLogs extends Model
{
    protected $fillable = [
        'cloud_id',
        'type',
        'created_at',
        'original_data'
    ];
}
