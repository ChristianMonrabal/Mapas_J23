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
            'img' => "rest_icon.jpg",
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        Tag::create([
            'name' => 'Bar',
            'img' => "bar_icon.jpg",
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        Tag::create([
            'name' => 'Hospital',
            'img' => "hosp_icon.jpg",
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        Tag::create([
            'name' => 'Parque',
            'img' => "par_icon.jpg",
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        Tag::create([
            'name' => 'Centro de ocio',
            'img' => "ocio_icon.jpg",
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        Tag::create([
            'name' => 'Centro Cultural',
            'img' => "cult_icon.jpg",
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        Tag::create([
            'name' => 'Estanco',
            'img' => "estnc_icon.jpg",
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        Tag::create([
            'name' => 'Supermercado',
            'img' => "supermr_icon.jpg",
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        Tag::create([
            'name' => 'Polideportivo',
            'img' => "polid_icon.jpg",
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        Tag::create([
            'name' => 'Estación de tren',
            'img' => "tren_icon.jpg",
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        Tag::create([
            'name' => 'Churrería',
            'img' => "churre_icon.jpg",
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        Tag::create([
            'name' => 'Cancha de baloncesto',
            'img' => "balonc_icon.jpg",
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        Tag::create([
            'name' => 'Capilla',
            'img' => "capll_icon.jpg",
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        Tag::create([
            'name' => 'Kebab',
            'img' => "keb_icon.jpg",
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }
}
