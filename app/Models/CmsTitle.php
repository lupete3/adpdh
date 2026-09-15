<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class CmsTitle extends Model {
 protected $fillable = ['key','label','value','sort_order'];
}
