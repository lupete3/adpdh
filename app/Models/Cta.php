<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cta extends Model
{
    protected $guarded = [];

    protected $casts = [
        'benefits' => 'array',
    ];
}
