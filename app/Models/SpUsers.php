<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpUsers extends Model
{
    protected $connection = 'mysql_studio';
    public $timestamps = false;

    protected $fillable = [
        'fullname',
    ];
}
