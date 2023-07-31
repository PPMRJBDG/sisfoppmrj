<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpWhatsappSchedules extends Model
{
    protected $connection = 'mysql_studio';
    public $timestamps = false;

    protected $fillable = [
        'ids',
        'team_id',
        'accounts',
        'next_account',
        'contact_id',
        'type',
        'template',
        'time_post',
        'min_delay',
        'schedule_time',
        'timezone',
        'max_delay',
        'name',
        'caption',
        'media',
        'sent',
        'failed',
        'result',
        'run',
        'status',
        'changed',
        'created'
    ];
}
