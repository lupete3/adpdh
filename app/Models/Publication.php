<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Publication extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected $casts = ['is_demo' => 'boolean', 'distribution_allowed' => 'boolean'];

    public function file()
    {
        return $this->belongsTo(MediaAsset::class, 'file_media_id');
    }

    public function cover()
    {
        return $this->belongsTo(MediaAsset::class, 'cover_media_id');
    }

    public function scopeForCms($query)
    {
        return $query->whereNotNull('cms_key');
    }

    public function scopePubliclyVisible($query)
    {
        return $query->forCms()->where('publication_state', 'published')->where('is_demo', false)->where('availability', 'available')->whereHas('file', fn ($file) => $file->where('disk', 'local')->where('mime_type', 'application/pdf')->where('publication_allowed', true)->where('is_demo', false));
    }

    public function hasReadableFile(): bool
    {
        $file = $this->file;

        return $file && $file->disk === 'local' && $file->mime_type === 'application/pdf'
            && $file->publication_allowed && ! $file->is_demo
            && str_starts_with($file->path, 'resources/') && ! str_contains($file->path, '..')
            && Storage::disk('local')->exists($file->path);
    }

    public function canDownload(): bool
    {
        return ! $this->trashed() && $this->cms_key && $this->publication_state === 'published'
            && ! $this->is_demo && $this->distribution_allowed && $this->availability === 'available'
            && $this->hasReadableFile();
    }
}
