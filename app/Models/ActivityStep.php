<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ActivityStep extends Model {
 protected $fillable = ['project_id', 'key', 'title', 'description', 'sort_order'];
 public $timestamps = false;
}
