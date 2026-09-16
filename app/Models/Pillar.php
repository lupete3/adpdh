<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pillar extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $fillable = ['key', 'title', 'description', 'sort_order', 'is_visible'];

    protected $casts = ['is_visible' => 'boolean'];

    public function axes()
    {
        return $this->hasMany(InterventionAxis::class)->orderBy('sort_order')->orderBy('id');
    }
}
