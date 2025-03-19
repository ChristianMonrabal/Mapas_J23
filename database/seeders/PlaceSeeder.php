<?php

namespace Database\Seeders;

use App\Models\Place;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlaceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        Place::create([
            'name' => 'Bar Alonso',
            'description' => 'bar de tapas y bocatas',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }
}
