<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InterventionAxis extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $fillable = ['pillar_id', 'key', 'title', 'description', 'sort_order', 'is_visible'];

    protected $casts = ['is_visible' => 'boolean'];

    public function pillar()
    {
        return $this->belongsTo(Pillar::class);
    }

    public function activities()
    {
        return $this->belongsToMany(Project::class, 'activity_axis');
    }
}
