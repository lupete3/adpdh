<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class CmsSection extends Model {
 protected $fillable = ['cms_page_id', 'key', 'label', 'template', 'eyebrow', 'title', 'title_accent', 'introduction', 'body', 'media_asset_id', 'collection', 'buttons', 'is_visible', 'sort_order'];
 protected $casts = ['body'=>'array','buttons'=>'array','is_visible'=>'boolean'];

 public function page(){return $this->belongsTo(CmsPage::class,'cms_page_id');}
 public function media(){return $this->belongsTo(MediaAsset::class,'media_asset_id');}
 public function revisions(){return $this->morphMany(ContentRevision::class,'revisable');}

 public function contents(){return $this->hasMany(CmsSectionContent::class)->orderBy('sort_order')->orderBy('id');}
}
