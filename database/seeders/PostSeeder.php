<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first() ?? User::create([
            'name' => 'Admin Wezesha',
            'email' => 'admin@wezesha-foundation.org',
            'password' => bcrypt('password'),
        ]);

        Post::query()->delete();

        Post::create([
            'title' => 'L\'importance de l\'éducation inclusive en RDC',
            'content' => "L'éducation est le socle de tout développement durable. À la Wezesha Foundation, nous croyons que chaque enfant, indépendamment de son origine sociale, mérite d'avoir accès à des outils d'apprentissage modernes. Notre dernier rapport montre une amélioration de 40% du taux de réussite chez les orphelins parrainés cette année...",
            'category' => 'Éducation',
            'status' => 'published',
            'image' => 'flexbiz/assets/img/blog/blog-1.jpg',
            'user_id' => $user->id,
        ]);

        Post::create([
            'title' => 'Autonomisation des femmes : Un levier pour la communauté',
            'content' => "Le bien-être social passe par l'autonomie financière des mères de famille. En formant les femmes des communautés locales à l'entrepreneuriat et à la gestion de petits commerces, nous renforçons la résilience des foyers. Ce mois-ci, 50 nouvelles femmes ont rejoint notre programme de micro-crédit à Goma...",
            'category' => 'Social',
            'status' => 'published',
            'image' => 'flexbiz/assets/img/blog/blog-2.jpg',
            'user_id' => $user->id,
        ]);

        Post::create([
            'title' => 'Retour sur notre distribution de kits scolaires 2024',
            'content' => "Grâce à vos dons, plus de 1000 enfants ont pu reprendre le chemin de l'école avec tout le matériel nécessaire. Stylos, cahiers, sacs et uniformes ont été distribués dans les zones rurales. Un grand merci à tous nos partenaires pour cet impact direct sur le futur de la jeunesse congolaise...",
            'category' => 'Humanitaire',
            'status' => 'published',
            'image' => 'flexbiz/assets/img/blog/blog-3.jpg',
            'user_id' => $user->id,
        ]);
    }
}