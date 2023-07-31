<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Liburan extends Model
{
    protected $fillable = [
        'liburan_from',
        'liburan_to',
        'keterangan',
    ];
}
