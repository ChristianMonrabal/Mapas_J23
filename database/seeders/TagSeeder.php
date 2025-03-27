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
            'img' => 'restaurante.png',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        Tag::create([
            'name' => 'Bar',
            'img' => 'bar.png',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        Tag::create([
            'name' => 'Hospital',
            'img' => 'hospital.png',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        Tag::create([
            'name' => 'Parque',
            'img' => 'parque.png',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        Tag::create([
            'name' => 'Centro de ocio',
            'img' => 'parque_ocio.png',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        Tag::create([
            'name' => 'Centro Cultural',
            'img' => 'centro_cultural.png',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        Tag::create([
            'name' => 'Estanco',
            'img' => 'estanco.png',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        Tag::create([
            'name' => 'Supermercado',
            'img' => 'supermercado.png',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        Tag::create([
            'name' => 'Polideportivo',
            'img' => 'polideportivo.png',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        Tag::create([
            'name' => 'Estación de tren',
            'img' => 'tren.png',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        Tag::create([
            'name' => 'Churrería',
            'img' => 'churreria.png',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        Tag::create([
            'name' => 'Cancha de baloncesto',
            'img' => 'cancha-de-baloncesto.png',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        Tag::create([
            'name' => 'Capilla',
            'img' => 'capilla.png',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        Tag::create([
            'name' => 'Kebab',
            'img' => 'kebab.png',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }
}
