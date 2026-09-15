<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    use HasFactory;

    protected $guarded = [];
 protected $casts = ['body'=>'array','is_demo'=>'boolean','consent_at'=>'datetime'];
 public function activity(){return $this->belongsTo(Project::class,'project_id');}
 public function gallery(){return $this->belongsTo(Gallery::class);}
 public function scopeForCms($q){return $q->whereNotNull('cms_key');}
 public function scopePubliclyVisible($q){return $q->forCms()->where('publication_state','published')->where('is_demo',false)->whereNotNull('consent_at');}
}