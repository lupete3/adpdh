<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
 protected $casts = ['body'=>'array','is_demo'=>'boolean','published_at'=>'datetime'];
 public function cover(){return $this->belongsTo(MediaAsset::class,'cover_media_id');}
 public function scopeForCms($q){return $q->whereNotNull('cms_key');}
 public function scopePubliclyVisible($q){return $q->forCms()->where('status','published')->where('is_demo',false)->whereNotNull('published_at')->where('published_at','<=',now());}
}