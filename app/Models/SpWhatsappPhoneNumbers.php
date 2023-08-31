<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\SpWhatsappContacts;

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

    public function contact()
    {
        return $this->belongsTo(SpWhatsappContacts::class, 'pid', 'id');
    }
}
