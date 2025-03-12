<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DewanPengajars extends Model
{
    protected $fillable = [
        'name',
        'pin',
        'cloud_fs1',
        'cloud_fs2',
        'cloud_fs3',
    ];
}
