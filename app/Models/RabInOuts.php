<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RabInOuts extends Model
{
    protected $fillable = [
        'pos',
        'fkDivisi_id',
        'fkRab_id',
        'tanggal',
        'jenis',
        'uraian',
        'qty',
        'nominal',
        'is_deleted',
        'created_by'
    ];

    public function divisi()
    {
        return $this->belongsTo(Divisis::class, 'fkDivisi_id');
    }

    public function rab()
    {
        return $this->belongsTo(Rabs::class, 'fkRab_id');
    }
}
