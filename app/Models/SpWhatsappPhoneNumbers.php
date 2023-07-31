<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpWhatsappPhoneNumbers extends Model
{
    protected $connection = 'mysql_studio';
    public $timestamps = false;

    protected $fillable = [
        'ids',
        'team_id',
        'pid',
        'phone',
    ];
}
