<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamMember extends Model
{
    use HasFactory;

    protected $guarded = [];
 protected $casts = ['show_contacts'=>'boolean'];
 public function portrait(){return $this->belongsTo(MediaAsset::class,'photo_media_id');}
 public function scopeForCms($q){return $q->whereNotNull('cms_key');}
}