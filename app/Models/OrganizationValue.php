<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class OrganizationValue extends Model {
 protected $fillable = ['key', 'title', 'description', 'sort_order', 'is_visible'];
 protected $casts = ['is_visible'=>'boolean'];

}
