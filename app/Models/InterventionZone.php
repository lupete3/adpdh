<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class InterventionZone extends Model {
 protected $fillable = ['key', 'title', 'description', 'zone_status', 'sort_order', 'is_visible'];
 protected $casts = ['is_visible'=>'boolean'];

}
