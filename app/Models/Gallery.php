<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Gallery extends Model {
 protected $fillable = ['key', 'title', 'description'];
 public function items(){return $this->hasMany(GalleryItem::class)->orderBy('sort_order')->orderBy('id');}
}
