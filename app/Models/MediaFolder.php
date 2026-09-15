<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class MediaFolder extends Model {
 protected $fillable = ['key', 'name'];
 public function assets(){return $this->hasMany(MediaAsset::class);}
}
