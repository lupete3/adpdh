<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ContentRevision extends Model {
 protected $fillable = ['snapshot', 'reason', 'user_id'];
 protected $casts = ['snapshot'=>'array'];
 public function revisable(){return $this->morphTo();}
}
