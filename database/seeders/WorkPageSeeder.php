<?php

namespace Database\Seeders;

use App\Models\CmsPage;
use App\Models\CmsSectionContent;
use Illuminate\Database\Seeder;

class WorkPageSeeder extends Seeder
{
    public function run(): void
    {
        $page = CmsPage::where('key', 'que-faisons-nous')->firstOrFail();
        $hero = $page->sections()->where('key', 'hero')->firstOrFail();
        // Convert only the original wording; preserve any custom editorial text.
        if (trim($hero->title) === 'Quatre piliers.') {
            $hero->title = '{Piliers}.';
        }
        if ($hero->introduction === 'Le Plan stratégique 2026–2030 articule neuf axes d’intervention autour de quatre piliers complémentaires.') {
            $hero->introduction = 'Le Plan stratégique 2026–2030 articule {axes} d’intervention autour de {piliers} {complementaires}.';
        }
        $hero->save();
        CmsSectionContent::whereHas('section', fn ($q) => $q->where('key', 'footer')->whereHas('page', fn ($p) => $p->where('key', 'index')))
            ->where('title', 'Nos quatre piliers')->where('link_url', 'que-faisons-nous.html')->update(['title' => 'Nos piliers']);
        if ($page->seo_description === 'Quatre piliers et neuf axes pour le développement, la protection et les droits humains.') {
            $page->update(['seo_description' => '{Piliers} et {axes} pour le développement, la protection et les droits humains.']);
        }
    }
}
