<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FsLogs extends Model
{
    protected $fillable = [
        'cloud_id',
        'type',
        'created_at',
        'original_data'
    ];
}
