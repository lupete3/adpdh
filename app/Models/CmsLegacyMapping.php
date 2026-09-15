<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class CmsLegacyMapping extends Model {
 protected $fillable = ['cms_title_id', 'target_type', 'target_id', 'target_field', 'imported_value'];

}
