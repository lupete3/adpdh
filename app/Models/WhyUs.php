<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhyUs extends Model
{
    protected $guarded = [];

    protected $casts = [
        'intro_highlights' => 'array',
    ];
}
