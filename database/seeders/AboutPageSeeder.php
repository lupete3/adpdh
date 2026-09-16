<?php

namespace Database\Seeders;

use App\Models\CmsPage;
use App\Models\MediaAsset;
use Illuminate\Database\Seeder;

class AboutPageSeeder extends Seeder
{
    public function run(): void
    {
        $page = CmsPage::where('key', 'qui-sommes-nous')->firstOrFail();
        $page->sections()->firstOrCreate(['key' => 'construire-ensemble'], [
            'label' => 'Construire ensemble',
            'template' => 'call-to-action',
            'eyebrow' => 'CONSTRUIRE ENSEMBLE',
            'title' => 'Partageons nos forces.',
            'title_accent' => 'Amplifions notre action.',
            'buttons' => [['label' => 'Devenir partenaire', 'url' => 'devenir-partenaire.html']],
            'is_visible' => true,
            'sort_order' => ($page->sections()->max('sort_order') ?? 7) + 1,
        ]);
        // Initialise only untouched imports; running this again preserves editorial changes.
        foreach (['hero', 'histoire', 'vision', 'mission', 'valeurs', 'statut', 'zones', 'equipe'] as $order => $key) {
            $section = $page->sections()->where('key', $key)->firstOrFail();
            if ($section->version !== 1) {
                continue;
            }
            $section->sort_order = $order;
            if (in_array($key, ['hero', 'valeurs'])) {
                $section->image_caption ??= 'Image d’illustration générée par IA · à remplacer';
            }
            if ($key === 'hero') {
                $section->image_note_title ??= 'Depuis 2010';
                $section->image_note_text ??= 'Développement · Protection · Droits humains';
            }
            if ($key === 'equipe' && $section->introduction === 'Coordonnateur') {
                $section->introduction = null;
            }
            if (in_array($key, ['hero', 'valeurs']) && ! $section->media_asset_id) {
                $section->media_asset_id = MediaAsset::where('key', $key === 'hero' ? 'community' : 'workshop')->value('id');
            }
            $section->save();
        }
        $legal = $page->sections()->where('key', 'statut')->firstOrFail();
        foreach ([
            ['denomination', 'Dénomination', 'Action pour le Développement et la Promotion des Droits Humains — ADPDH'],
            ['statut', 'Statut', 'Association sans but lucratif, apolitique, de développement communautaire et des droits humains'],
            ['creation', 'Création', '10 janvier 2010, à Bukavu'],
            ['siege', 'Siège', '305, avenue Patrice Emery Lumumba, commune d’Ibanda, Bukavu, RDC'],
        ] as $order => [$key, $title, $description]) {
            $legal->contents()->withTrashed()->firstOrCreate(['key' => $key], ['title' => $title, 'description' => $description, 'sort_order' => $order, 'is_visible' => true]);
        }
    }
}
