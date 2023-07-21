<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Materi extends Model
{
    protected $fillable = [
        'name',
        'pageNumbers',
        'for'
    ];
}

