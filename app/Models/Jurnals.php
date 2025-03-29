<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Divisies;
use App\Models\Rabs;
use App\Models\Banks;
use App\Models\Poses;
use App\Models\RabManagBuildings;

class Jurnals extends Model
{
    protected $fillable = [
        'fkBank_id',
        'fkPos_id',
        'fkDivisi_id',
        'fkRab_id',
        'tanggal',
        'jenis',
        'sub_jenis',
        'uraian',
        'qty',
        'nominal',
        'tipe_pengeluaran',
        'tipe_penerimaan',
        'periode_tahun',
        'is_deleted',
        'created_by',
        'fkSodaqoh_id',
        'fkRabManagBuilding_id',
    ];

    public function divisi()
    {
        return $this->belongsTo(Divisies::class, 'fkDivisi_id');
    }

    public function managBuilding()
    {
        return $this->belongsTo(RabManagBuildings::class, 'fkRabManagBuilding_id');
    }

    public function rab()
    {
        return $this->belongsTo(Rabs::class, 'fkRab_id');
    }

    public function bank()
    {
        return $this->belongsTo(Banks::class, 'fkBank_id');
    }

    public function pos()
    {
        return $this->belongsTo(Poses::class, 'fkPos_id');
    }
}
