<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Indicator extends Model {
 protected $fillable = ['key', 'title', 'unit', 'description', 'project_id', 'sort_order', 'is_visible'];
 protected $casts = ['is_visible'=>'boolean'];

 public function values(){return $this->hasMany(IndicatorValue::class)->orderByDesc('id');}
 public function currentValue(){return $this->hasOne(IndicatorValue::class)->latestOfMany();}
 public function activity(){return $this->belongsTo(Project::class,'project_id');}

}
