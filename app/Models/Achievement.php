<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Achievement extends Model
{
    use HasFactory;

    protected $fillable = [
        'partner_id',
        'title',
        'description',
        'image',
        'date',
        'location',
    ];

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }
}