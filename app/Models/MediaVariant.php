<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class MediaVariant extends Model {
 protected $fillable = ['media_asset_id', 'name', 'path', 'mime_type', 'width', 'height', 'size'];
 public $timestamps = false;
}
