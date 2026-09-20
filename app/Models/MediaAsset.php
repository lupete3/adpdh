<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class MediaAsset extends Model {
 protected $fillable = ['checksum', 'key', 'media_folder_id', 'name', 'kind', 'disk', 'path', 'visibility', 'mime_type', 'size', 'width', 'height', 'alt', 'caption', 'credit', 'source', 'is_illustration', 'is_demo', 'publication_allowed', 'uploaded_by'];
 protected $casts = ['is_illustration'=>'boolean','is_demo'=>'boolean','publication_allowed'=>'boolean'];

 public function folder(){return $this->belongsTo(MediaFolder::class,'media_folder_id');}
 public function variants(){return $this->hasMany(MediaVariant::class);}
 public function isPubliclyAvailable(): bool {
  if($this->visibility!=='public'||!$this->publication_allowed||$this->is_demo)return false;
  if(str_contains($this->path,'..')||str_starts_with($this->path,'/'))return false;
  return $this->disk==='builtin' ? is_file(public_path($this->path)) : ($this->disk==='public' && \Illuminate\Support\Facades\Storage::disk('public')->exists($this->path));
 }
 public function publicUrl(): ?string {if(!$this->isPubliclyAvailable())return null;return $this->disk==='builtin'?asset($this->path):\Illuminate\Support\Facades\Storage::disk('public')->url($this->path);}

}
