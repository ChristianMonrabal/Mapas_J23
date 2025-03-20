<?php

namespace Database\Seeders;

use App\Models\PlaceTag;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlaceTagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        PlaceTag::create([
            'place_id' => 1,
            'tag_id' => 2,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        PlaceTag::create([
            'place_id' => 2,
            'tag_id' => 13,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        PlaceTag::create([
            'place_id' => 3,
            'tag_id' => 4,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        PlaceTag::create([
            'place_id' => 4,
            'tag_id' => 6,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        PlaceTag::create([
            'place_id' => 5,
            'tag_id' => 12,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        PlaceTag::create([
            'place_id' => 6,
            'tag_id' => 5,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        PlaceTag::create([
            'place_id' => 7,
            'tag_id' => 4,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        PlaceTag::create([
            'place_id' => 8,
            'tag_id' => 11,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        PlaceTag::create([
            'place_id' => 9,
            'tag_id' => 10,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        PlaceTag::create([
            'place_id' => 10,
            'tag_id' => 9,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        PlaceTag::create([
            'place_id' => 11,
            'tag_id' => 8,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        PlaceTag::create([
            'place_id' => 12,
            'tag_id' => 7,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        PlaceTag::create([
            'place_id' => 13,
            'tag_id' => 3,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        PlaceTag::create([
            'place_id' => 14,
            'tag_id' => 14,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }
}
