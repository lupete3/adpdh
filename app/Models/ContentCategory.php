<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ContentCategory extends Model {
 protected $fillable = ['kind', 'slug', 'name', 'sort_order'];

}
