<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Divisies;
use App\Models\Rabs;

class RabInouts extends Model
{
    protected $fillable = [
        'posisi',
        'pos',
        'fkDivisi_id',
        'fkRab_id',
        'tanggal',
        'jenis',
        'uraian',
        'qty',
        'nominal',
        'tipe_pengeluaran',
        'tipe_penerimaan',
        'periode_tahun',
        'is_deleted',
        'created_by',
        'fkSodaqoh_id'
    ];

    public function divisi()
    {
        return $this->belongsTo(Divisies::class, 'fkDivisi_id');
    }

    public function rab()
    {
        return $this->belongsTo(Rabs::class, 'fkRab_id');
    }
}
