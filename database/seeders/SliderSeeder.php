<?php

namespace Database\Seeders;

use App\Models\Slider;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SliderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Slider::query()->delete();
        Slider::create([
            'title' => 'WEZESHA FOUNDATION',
            'subtitle' => 'TRANSFORMING THE FUTURE OF DRC',
            'description' => 'Promouvoir une éducation de qualité et le bien-être social des orphelins et familles défavorisées en RDC.',
            'image' => 'flexbiz/assets/img/hero-bg.jpg',
            'secondary_image' => 'flexbiz/assets/img/illustration/illustration-15.webp',
            'floating_badge' => '10+ Ans d\'Impact',
            'button1_text' => 'En Savoir Plus',
            'button1_url' => '#about',
            'button2_text' => 'Faire un Don',
            'button2_url' => '#',
            'mini_stats' => [
                ['icon' => 'bi bi-mortarboard', 'label' => 'Éducation'],
                ['icon' => 'bi bi-heart', 'label' => 'Humanitaire'],
                ['icon' => 'bi bi-shield-check', 'label' => 'Impact']
            ],
            'order' => 1,
        ]);

        Slider::create([
            'title' => 'Éduquer, Autonomiser, Transformer',
            'subtitle' => 'Un Engagement pour la RDC',
            'description' => 'Soutenir les enfants orphelins pour un avenir meilleur et une société plus juste.',
            'image' => 'flexbiz/assets/img/features-3.jpg',
            'secondary_image' => 'flexbiz/assets/img/illustration/illustration-7.webp',
            'floating_badge' => 'Engagement Local',
            'button1_text' => 'Nos Projets',
            'button1_url' => '#portfolio',
            'button2_text' => 'Nous Contacter',
            'button2_url' => '#contact',
            'mini_stats' => [
                ['icon' => 'bi bi-people', 'label' => 'Communauté'],
                ['icon' => 'bi bi-lightbulb', 'label' => 'Innovation'],
                ['icon' => 'bi bi-globe', 'label' => 'Vision']
            ],
            'order' => 2,
        ]);
    }
}