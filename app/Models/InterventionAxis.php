<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class InterventionAxis extends Model {
 protected $fillable = ['pillar_id', 'key', 'title', 'description', 'sort_order'];
 public function pillar(){return $this->belongsTo(Pillar::class);}
 public function activities(){return $this->belongsToMany(Project::class,'activity_axis');}
}
