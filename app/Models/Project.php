<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    /** @use HasFactory<\Database\Factories\ProjectFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'category',
        'client',
        'date',
        'description',
        'content',
        'image',
        'url',
        'cms_key','activity_status','publication_state','is_demo','objective','audience','location','period_label','start_year','end_year','results_note','source_note','body','sort_order','cover_media_id','gallery_id','content_category_id',
    ];
 protected $casts = ['body'=>'array','is_demo'=>'boolean'];
 public function axes(){return $this->belongsToMany(InterventionAxis::class,'activity_axis');}
 public function steps(){return $this->hasMany(ActivityStep::class)->orderBy('sort_order');}
 public function indicators(){return $this->hasMany(Indicator::class);}
 public function gallery(){return $this->belongsTo(Gallery::class);}
 public function cover(){return $this->belongsTo(MediaAsset::class,'cover_media_id');}
 public function scopeForCms($q){return $q->whereNotNull('cms_key');}
 public function scopePubliclyVisible($q){return $q->forCms()->where('publication_state','published')->where('is_demo',false);}
}
