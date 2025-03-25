<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RolSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(GymkhanaSeeder::class); // Se ejecuta antes de GroupSeeder
        $this->call(GroupSeeder::class);
        $this->call(GroupUserSeeder::class);
        $this->call(TagSeeder::class);
        $this->call(PlaceSeeder::class);
        $this->call(PlaceTagSeeder::class);
        $this->call(FavoriteSeeder::class);
        $this->call(CheckpointSeeder::class);
        $this->call(GymkhanaProgressSeeder::class);
    }
}
