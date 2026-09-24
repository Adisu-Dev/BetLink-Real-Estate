<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            EthiopianRegionsSeeder::class,
            PropertyTypeSeeder::class,
            AmenitySeeder::class,
            SystemSettingsSeeder::class,
            AdminUserSeeder::class,
        ]);
    }
}
