<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class GalleryItem extends Model {
 protected $fillable = ['gallery_id', 'media_asset_id', 'caption', 'alt', 'sort_order'];
 public function media(){return $this->belongsTo(MediaAsset::class,'media_asset_id');}
}
