<?php

namespace Database\Seeders;

use App\Models\CmsPage;
use App\Models\Indicator;
use App\Models\MediaAsset;
use App\Models\Pillar;
use App\Models\Project;
use Illuminate\Database\Seeder;

class HomeContentsSeeder extends Seeder
{
    public function run(): void
    {
        $page = CmsPage::where('key', 'index')->firstOrFail();
        $page->sections()->firstOrCreate(['key' => 'footer'], ['label' => 'Pied de page', 'template' => 'footer', 'title' => 'ADPDH', 'introduction' => 'Action pour le Développement et la Promotion des Droits Humains.', 'eyebrow' => 'Bukavu · République démocratique du Congo', 'title_accent' => 'La dignité humaine au centre de chaque intervention.', 'collection' => 'footer_links', 'sort_order' => 100]);
        $sections = $page->sections()->get()->keyBy('key');
        $source = json_decode(file_get_contents(database_path('content/adpdh-home-contents.json')), true);
        foreach ($source as $i => $row) {
            $section = $sections[$row['section']];
            if ($section->contents()->withTrashed()->where('key', $row['key'])->exists()) {
                continue;
            }
            $data = collect($row)->only(['key', 'title', 'subtitle', 'description', 'detail_title', 'detail_text', 'link_label', 'link_url', 'is_demo', 'is_visible'])->all();
            $data['sort_order'] = $i;
            if (isset($row['indicator_key'])) {
                $data['indicator_id'] = Indicator::where('key', $row['indicator_key'])->value('id');
            }
            if (! empty($row['image'])) {
                $path = public_path('adpdh/'.$row['image']);
                if (is_file($path)) {
                    $size = getimagesize($path);
                    $media = MediaAsset::firstOrCreate(['disk' => 'builtin', 'path' => 'adpdh/'.$row['image']], ['key' => 'home-content-'.md5($row['image']), 'name' => basename($path), 'kind' => 'image', 'visibility' => 'public', 'mime_type' => $size['mime'], 'size' => filesize($path), 'width' => $size[0], 'height' => $size[1], 'alt' => 'Image de la maquette ADPDH', 'publication_allowed' => true]);
                    $data['media_asset_id'] = $media->id;
                }
            }
            // Keep user edits to shared records when they differ from their initial imported values.
            if (isset($row['source_kind'])) {
                $class = $row['source_kind'] === 'pillars' ? Pillar::class : Project::class;
                $sourceDefinition = json_decode(file_get_contents(database_path('content/adpdh-structured.json')), true)[$row['source_kind']][$row['source_index']];
                $record = $class::where($class === Project::class ? 'cms_key' : 'key', $sourceDefinition['key'])->first();
                if ($record) {
                    $data['sort_order'] = $record->sort_order;
                    $map = \App\Models\CmsLegacyMapping::where('target_type', $class)->where('target_id', $record->id)->where('target_field', 'title')->first();
                    if ($map && trim($record->title) !== trim($map->imported_value)) {
                        $data['title'] = $record->title;
                    }
                    $initial = json_decode(file_get_contents(database_path('content/adpdh-structured.json')), true)[$row['source_kind']][$row['source_index']]['description'] ?? null;
                    if ($record->description !== $initial) {
                        $data['description'] = $record->description;
                    }
                }
            }
            $section->contents()->create($data);
        }
    }
}
