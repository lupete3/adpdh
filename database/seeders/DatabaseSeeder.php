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
            ['email' => 'contact@adpdh.org'],
            [
                'name' => 'Admin ADPDH',
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
