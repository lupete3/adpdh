<?php

namespace Database\Seeders;

use App\Models\Skill;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SkillSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $skills = [
            ['title' => 'Éducation & Parrainage', 'percentage' => 95, 'description' => 'Taux de réussite scolaire des enfants parrainés.'],
            ['title' => 'Sécurité Alimentaire', 'percentage' => 85, 'description' => 'Augmentation moyenne des récoltes des familles accompagnées.'],
            ['title' => 'Autonomisation', 'percentage' => 90, 'description' => 'Taux de remboursement des micro-crédits solidaires.'],
            ['title' => 'Santé Communautaire', 'percentage' => 80, 'description' => 'Couverture des soins de base dans nos zones d\'intervention.'],
        ];

        foreach ($skills as $index => $s) {
            Skill::create([
                'title' => $s['title'],
                'percentage' => $s['percentage'],
                'description' => $s['description'],
                'order' => $index
            ]);
        }
    }
}
