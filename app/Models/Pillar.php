<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Pillar extends Model {
 protected $fillable = ['key', 'title', 'description', 'sort_order'];
 public function axes(){return $this->hasMany(InterventionAxis::class)->orderBy('sort_order');}
}
