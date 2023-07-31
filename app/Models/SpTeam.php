<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpTeam extends Model
{
    protected $connection = 'mysql_studio';
    protected $table = 'sp_team';
    public $timestamps = false;

    protected $fillable = [
        'owner',
    ];

    public function user()
    {
        return $this->belongsTo(SpUsers::class, 'owner', 'id');
    }
}
