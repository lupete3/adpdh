<?php

namespace Database\Seeders;

use App\Models\Feature;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FeatureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Feature::create([
            'icon' => 'fa-sitemap',
            'title' => 'Réseautage Stratégique',
            'description' => "Connectez-vous avec des organisations partageant les mêmes visions à travers l'Afrique.",
            'order' => 1,
        ]);

        Feature::create([
            'icon' => 'fa-bullhorn',
            'title' => 'Visibilité Accrue',
            'description' => "Publiez vos activités sur notre blog pour atteindre une audience plus large.",
            'order' => 2,
        ]);

        Feature::create([
            'icon' => 'fa-users-cog',
            'title' => 'Partage de Connaissances',
            'description' => "Accédez à des ressources et des expertises partagées par les membres du réseau.",
            'order' => 3,
        ]);

        Feature::create([
            'icon' => 'fa-handshake',
            'title' => 'Opportunités de Collaboration',
            'description' => "Trouvez des partenaires pour vos projets et répondez à des appels à propositions communs.",
            'order' => 4,
        ]);
    }
}