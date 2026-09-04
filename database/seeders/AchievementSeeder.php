<?php

namespace Database\Seeders;

use App\Models\Achievement;
use App\Models\Partner;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AchievementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $partners = Partner::all();

        if ($partners->isEmpty()) {
            $this->command->info('No partners found, skipping achievement seeder.');
            return;
        }

        Achievement::create([
            'partner_id' => $partners->random()->id,
            'title' => 'Construction de 10 puits au Mali',
            'description' => 'Accès à l\'eau potable pour plus de 5000 personnes dans la région de Ségou.',
            'image' => 'template/img/blog-1.jpg',
            'date' => '2024-05-20',
            'location' => 'Ségou, Mali'
        ]);

        Achievement::create([
            'partner_id' => $partners->random()->id,
            'title' => 'Programme de nutrition pour enfants',
            'description' => 'Distribution de repas nutritifs à 2000 enfants dans des écoles primaires à Madagascar.',
            'image' => 'template/img/blog-2.jpg',
            'date' => '2024-06-15',
            'location' => 'Antananarivo, Madagascar'
        ]);

        Achievement::create([
            'partner_id' => $partners->random()->id,
            'title' => 'Formation agricole pour 100 femmes',
            'description' => 'Renforcement des compétences en agriculture durable pour les femmes rurales au Sénégal.',
            'image' => 'template/img/blog-3.jpg',
            'date' => '2024-07-01',
            'location' => 'Kaolack, Sénégal'
        ]);
    }
}