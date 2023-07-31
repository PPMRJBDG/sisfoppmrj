<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpWhatsappContacts extends Model
{
    protected $connection = 'mysql_studio';
    public $timestamps = false;

    protected $fillable = [
        'ids',
        'team_id',
        'name',
        'status',
        'changed',
        'created'
    ];
}
