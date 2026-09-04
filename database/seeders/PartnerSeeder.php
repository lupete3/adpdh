<?php

namespace Database\Seeders;

use App\Models\Partner;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PartnerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 1; $i <= 9; $i++) {
            Partner::create([
                'name' => 'Partenaire ' . $i,
                'logo' => 'template/img/vendor-' . $i . '.jpg',
                'website_url' => '#',
            ]);
        }
    }
}