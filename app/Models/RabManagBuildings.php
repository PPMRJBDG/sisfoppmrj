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
    ];

    public function details()
    {
        return $this->hasMany(RabManagBuildingDetails::class, 'fkRabManagBuilding_id');
    }

    public function total_biaya()
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
}
