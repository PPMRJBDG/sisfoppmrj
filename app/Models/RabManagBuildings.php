<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RabManagBuildings extends Model
{
    protected $fillable = [
        'nama',
        'periode_bulan',
        'status',
        'deskripsi'
    ];

    public function details()
    {
        return $this->hasMany(RabManagBuildingDetails::class, 'fkRabManagBuilding_id');
    }

    public function total_rab()
    {
        $details = RabManagBuildingDetails::where('fkRabManagBuilding_id',$this->id)->get();
        $total_biaya = 0;
        if($details!=null){
            foreach($details as $d){
                $total_biaya = $total_biaya + ($d->qty*$d->biaya);
            }
        }
        return $total_biaya;
    }

    public function total_realisasi()
    {
        $details = RabManagBuildingDetails::where('fkRabManagBuilding_id',$this->id)->get();
        $total_biaya = 0;
        if($details!=null){
            foreach($details as $d){
                $total_biaya = $total_biaya + ($d->qty_realisasi*$d->biaya_realisasi);
            }
        }
        return $total_biaya;
    }
}
