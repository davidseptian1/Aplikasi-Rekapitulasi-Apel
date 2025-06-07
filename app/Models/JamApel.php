<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JamApel extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'start_time',
        'end_time',
    ];
}
