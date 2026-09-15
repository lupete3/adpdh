<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CmsSectionContent extends Model
{
    use SoftDeletes;

    protected $fillable = ['key', 'title', 'subtitle', 'description', 'detail_title', 'detail_text', 'link_label', 'link_url', 'media_asset_id', 'indicator_id', 'is_visible', 'is_demo', 'sort_order'];

    protected $casts = ['is_visible' => 'boolean', 'is_demo' => 'boolean'];

    public function section()
    {
        return $this->belongsTo(CmsSection::class, 'cms_section_id');
    }

    public function media()
    {
        return $this->belongsTo(MediaAsset::class, 'media_asset_id');
    }

    public function indicator()
    {
        return $this->belongsTo(Indicator::class);
    }

    public function revisions()
    {
        return $this->morphMany(ContentRevision::class, 'revisable');
    }

    public function href(): ?string
    {
        if (! $this->link_url) {
            return null;
        }
        if (preg_match('~^(?:https?://|mailto:|tel:|/|#)~', $this->link_url)) {
            return $this->link_url;
        }

        return asset('adpdh/'.$this->link_url);
    }
}
