<?php

namespace Database\Seeders;

use App\Models\Testimonial;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TestimonialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Testimonial::create([
            'author_name' => 'Nom du Partenaire',
            'author_position' => 'Profession',
            'author_photo' => 'template/img/testimonial-1.jpg',
            'content' => '"Cette plateforme a transformé notre manière de collaborer. Nous avons pu nouer des partenariats solides et efficaces."',
        ]);

        Testimonial::create([
            'author_name' => 'Nom du Partenaire',
            'author_position' => 'Profession',
            'author_photo' => 'template/img/testimonial-2.jpg',
            'content' => '"Un outil indispensable pour la visibilité de nos actions sur le terrain. Simple, intuitif et très impactant."',
        ]);

        Testimonial::create([
            'author_name' => 'Nom du Partenaire',
            'author_position' => 'Profession',
            'author_photo' => 'template/img/testimonial-3.jpg',
            'content' => '"Grâce au réseau, nous avons trouvé des compétences que nous n\'avons pas en interne. Un vrai plus pour nos projets."',
        ]);

        Testimonial::create([
            'author_name' => 'Nom du Partenaire',
            'author_position' => 'Profession',
            'author_photo' => 'template/img/testimonial-4.jpg',
            'content' => '"La section blog est une excellente vitrine. Nos activités n\'ont jamais eu autant de résonance."',
        ]);
    }
}
