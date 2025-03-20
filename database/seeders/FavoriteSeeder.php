<?php

namespace Database\Seeders;

use App\Models\Favorite;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FavoriteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        Favorite::create([
            'user_id' => 1,
            'place_id' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        Favorite::create([
            'user_id' => 2,
            'place_id' => 2,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        Favorite::create([
            'user_id' => 2,
            'place_id' => 3,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        Favorite::create([
            'user_id' => 3,
            'place_id' => 3,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        Favorite::create([
            'user_id' => 3,
            'place_id' => 4,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        Favorite::create([
            'user_id' => 4,
            'place_id' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        Favorite::create([
            'user_id' => 4,
            'place_id' => 4,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }
}
