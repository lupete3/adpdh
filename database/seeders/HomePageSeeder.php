<?php

namespace Database\Seeders;

use App\Models\CmsPage;
use App\Models\MediaAsset;
use App\Models\Project;
use Illuminate\Database\Seeder;

class HomePageSeeder extends Seeder
{
    public function run(): void
    {
        $page = CmsPage::where('key', 'index')->firstOrFail();
        foreach (['hero' => 'adpdh_home.png', 'activity-home' => 'formation_avec.jpeg'] as $key => $file) {
            $path = public_path('adpdh/assets/'.$file);
            $size = getimagesize($path);
            $media = MediaAsset::firstOrCreate(['key' => 'home-'.$key], ['name' => $file, 'kind' => 'image', 'disk' => 'builtin', 'path' => 'adpdh/assets/'.$file, 'visibility' => 'public', 'mime_type' => $size['mime'], 'size' => filesize($path), 'width' => $size[0], 'height' => $size[1], 'alt' => 'ADPDH — développement et droits humains', 'source' => 'Image présente dans la maquette fournie', 'publication_allowed' => true]);
            if ($media->wasRecentlyCreated) {
                if ($key === 'hero') {
                    $page->sections()->where('key', 'hero')->whereNull('media_asset_id')->where('version', 1)->update(['media_asset_id' => $media->id]);
                } else {
                    Project::where('cms_key', 'avec-agr')->whereHas('cover', fn ($q) => $q->where('key', 'workshop'))->update(['cover_media_id' => $media->id]);
                }
            }
        }
        $page->sections()->where('key', 'activites')->where('version', 1)->update(['buttons' => json_encode([['label' => 'Toutes nos activités', 'url' => 'activites.html']])]);
        $page->sections()->where('key', 'actualites')->where('version', 1)->update(['buttons' => json_encode([['label' => 'Toutes les actualités', 'url' => 'actualites.html']])]);
        $page->sections()->where('key', 'contact')->where('version', 1)->update(['introduction' => 'Pour une question, une information ou un premier échange.']);
        $page->sections()->where('key', 'temoignages')->where('introduction', 'Les premiers témoignages réels sont en cours de collecte. Cet exemple présente le futur format des récits.')->update(['introduction' => 'Les premiers témoignages sont en cours de collecte. Retrouvez prochainement les récits des personnes accompagnées.']);
        $page->sections()->where('key', 'actualites')->where('introduction', 'Contenus de démonstration en attendant les premières publications validées par ADPDH.')->update(['introduction' => 'Retrouvez ici les nouvelles de nos activités et les publications de notre équipe.']);
    }
}
