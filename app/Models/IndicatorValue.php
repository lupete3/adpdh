<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class IndicatorValue extends Model {
 protected $fillable = ['indicator_id', 'value', 'period_label', 'followup_months', 'scope', 'source', 'method', 'limitations', 'change_note', 'recorded_by'];
 protected $casts = ['value'=>'decimal:4','followup_months'=>'integer'];
 public function indicator(){return $this->belongsTo(Indicator::class);}
}
