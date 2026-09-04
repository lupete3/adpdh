<?php

namespace Database\Seeders;

use App\Models\About;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AboutSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        About::create([
            'title' => 'Une génération d’enfants orphelins éduqués, autonomes et résiliants',
            'subtitle' => 'Qui sommes-nous ?',
            'content' => "WEZESHA FOUNDATION est dédiée à la promotion d’une éducation de qualité et équitable pour tous les enfants orphelins, ainsi qu’au bien-être social des familles économiquement défavorisées en RDC. Nous œuvrons pour transformer durablement la société à travers l'autonomisation et le développement communautaire.",
            'image' => 'flexbiz/assets/img/about.jpg',
            'kicker' => 'Notre Vision',
            'badge_title' => '10+ ans',
            'badge_text' => 'D\'impact social',
            'metrics' => [
                ['value' => '120+', 'label' => 'Orphelins'],
                ['value' => '10k+', 'label' => 'Familles'],
                ['value' => '95%', 'label' => 'Réussite']
            ],
            'button_text' => 'Notre Histoire',
            'button_url' => '#',
            'video_url' => '#',
            'features' => json_encode([
                'Vision: Éducation & Autonomie',
                'Mission: Soutien aux Familles',
                'Valeurs: Transparence & Impact',
                'Engagement: Développement Local',
            ]),
        ]);
    }
}