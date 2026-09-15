<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class CmsPage extends Model {
 protected $fillable = ['key','label','template','page_kind','seo_title','seo_description'];
 public function titles() { return $this->hasMany(CmsTitle::class)->orderBy('sort_order'); }
 public function sections(){return $this->hasMany(CmsSection::class)->orderBy('sort_order');}
}
