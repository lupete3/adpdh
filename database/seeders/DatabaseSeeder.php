<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@wezesha-foundation.org'],
            [
                'name' => 'Admin Wezesha',
                'password' => bcrypt('password'),
            ]
        );

        $this->call([
            FlexBizSeeder::class,
            SettingSeeder::class,
            PostSeeder::class,
        ]);
    }
}
