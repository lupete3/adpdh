<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Content seeds never reset accounts or reload the legacy theme.
        $this->call([CmsTitlesSeeder::class, StructuredCmsSeeder::class, HomePageSeeder::class, HomeContentsSeeder::class]);
    }
}
