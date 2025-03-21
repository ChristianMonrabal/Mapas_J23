<?php

namespace Database\Seeders;

use App\Models\Tag;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        Tag::create([
            'name' => 'Restaurante',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        Tag::create([
            'name' => 'Bar',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        Tag::create([
            'name' => 'Hospital',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        Tag::create([
            'name' => 'Parque',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        Tag::create([
            'name' => 'Centro de ocio',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        Tag::create([
            'name' => 'Centro Cultural',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        Tag::create([
            'name' => 'Estanco',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        Tag::create([
            'name' => 'Supermercado',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        Tag::create([
            'name' => 'Polideportivo',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        Tag::create([
            'name' => 'Estación de tren',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        Tag::create([
            'name' => 'Churrería',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        Tag::create([
            'name' => 'Cancha de baloncesto',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        Tag::create([
            'name' => 'Capilla',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        Tag::create([
            'name' => 'Kebab',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }
}
