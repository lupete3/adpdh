<?php

namespace Database\Seeders;

use App\Models\TeamMember;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TeamMemberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TeamMember::create([
            'name' => 'Nom Complet',
            'position' => 'Organisation A',
            'photo' => 'template/img/team-1.jpg',
            'twitter_url' => '#',
            'facebook_url' => '#',
            'linkedin_url' => '#',
        ]);

        TeamMember::create([
            'name' => 'Nom Complet',
            'position' => 'Organisation B',
            'photo' => 'template/img/team-2.jpg',
            'twitter_url' => '#',
            'facebook_url' => '#',
            'linkedin_url' => '#',
        ]);

        TeamMember::create([
            'name' => 'Nom Complet',
            'position' => 'Organisation C',
            'photo' => 'template/img/team-3.jpg',
            'twitter_url' => '#',
            'facebook_url' => '#',
            'linkedin_url' => '#',
        ]);
    }
}