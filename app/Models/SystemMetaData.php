<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemMetaData extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'key',
        'value'
    ];
}
