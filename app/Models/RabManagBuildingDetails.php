<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RabManagBuildingDetails extends Model
{
    protected $fillable = [
        'fkRabManagBuilding_id',
        'uraian',
        'qty',
        'satuan',
        'biaya',
        'qty_realisasi',
        'satuan_realisasi',
        'biaya_realisasi',
    ];

    public function mbuild()
    {
        return $this->belongsTo(RabManagBuildings::class, 'fkRabManagBuilding_id');
    }
}
