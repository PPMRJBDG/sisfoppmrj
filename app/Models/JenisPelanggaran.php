<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisPelanggaran extends Model
{
    protected $fillable = [
        'id',
        'jenis_pelanggaran',
        'kategori_pelanggaran'
    ];
}
