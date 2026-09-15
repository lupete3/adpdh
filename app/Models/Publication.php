<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Publication extends Model
{
    protected $guarded = [];
 protected $casts = ['is_demo'=>'boolean','distribution_allowed'=>'boolean'];
 public function file(){return $this->belongsTo(MediaAsset::class,'file_media_id');}
 public function cover(){return $this->belongsTo(MediaAsset::class,'cover_media_id');}
 public function scopeForCms($q){return $q->whereNotNull('cms_key');}
 public function canDownload(): bool {return $this->publication_state==='published' && !$this->is_demo && $this->distribution_allowed && $this->availability==='available' && ($this->file?->isPubliclyAvailable() ?? false);}
}
