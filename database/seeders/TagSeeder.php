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
<<<<<<< HEAD
            'img' => 'restaurante.png',
=======
            'img' => "rest_icon.jpg",
>>>>>>> dcd565cb6b8ffb4893942ed08f17ca3f7a91505b
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        Tag::create([
            'name' => 'Bar',
<<<<<<< HEAD
            'img' => 'bar.png',
=======
            'img' => "bar_icon.jpg",
>>>>>>> dcd565cb6b8ffb4893942ed08f17ca3f7a91505b
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        Tag::create([
            'name' => 'Hospital',
<<<<<<< HEAD
            'img' => 'hospital.png',
=======
            'img' => "hosp_icon.jpg",
>>>>>>> dcd565cb6b8ffb4893942ed08f17ca3f7a91505b
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        Tag::create([
            'name' => 'Parque',
<<<<<<< HEAD
            'img' => 'parque.png',
=======
            'img' => "par_icon.jpg",
>>>>>>> dcd565cb6b8ffb4893942ed08f17ca3f7a91505b
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        Tag::create([
            'name' => 'Centro de ocio',
<<<<<<< HEAD
            'img' => 'parque_ocio.png',
=======
            'img' => "ocio_icon.jpg",
>>>>>>> dcd565cb6b8ffb4893942ed08f17ca3f7a91505b
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        Tag::create([
            'name' => 'Centro Cultural',
<<<<<<< HEAD
            'img' => 'centro_cultural.png',
=======
            'img' => "cult_icon.jpg",
>>>>>>> dcd565cb6b8ffb4893942ed08f17ca3f7a91505b
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        Tag::create([
            'name' => 'Estanco',
<<<<<<< HEAD
            'img' => 'estanco.png',
=======
            'img' => "estnc_icon.jpg",
>>>>>>> dcd565cb6b8ffb4893942ed08f17ca3f7a91505b
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        Tag::create([
            'name' => 'Supermercado',
<<<<<<< HEAD
            'img' => 'supermercado.png',
=======
            'img' => "supermr_icon.jpg",
>>>>>>> dcd565cb6b8ffb4893942ed08f17ca3f7a91505b
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        Tag::create([
            'name' => 'Polideportivo',
<<<<<<< HEAD
            'img' => 'polideportivo.png',
=======
            'img' => "polid_icon.jpg",
>>>>>>> dcd565cb6b8ffb4893942ed08f17ca3f7a91505b
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        Tag::create([
            'name' => 'Estación de tren',
<<<<<<< HEAD
            'img' => 'tren.png',
=======
            'img' => "tren_icon.jpg",
>>>>>>> dcd565cb6b8ffb4893942ed08f17ca3f7a91505b
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        Tag::create([
            'name' => 'Churrería',
<<<<<<< HEAD
            'img' => 'churreria.png',
=======
            'img' => "churre_icon.jpg",
>>>>>>> dcd565cb6b8ffb4893942ed08f17ca3f7a91505b
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        Tag::create([
            'name' => 'Cancha de baloncesto',
<<<<<<< HEAD
            'img' => 'cancha-de-baloncesto.png',
=======
            'img' => "balonc_icon.jpg",
>>>>>>> dcd565cb6b8ffb4893942ed08f17ca3f7a91505b
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        Tag::create([
            'name' => 'Capilla',
<<<<<<< HEAD
            'img' => 'capilla.png',
=======
            'img' => "capll_icon.jpg",
>>>>>>> dcd565cb6b8ffb4893942ed08f17ca3f7a91505b
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        Tag::create([
            'name' => 'Kebab',
<<<<<<< HEAD
            'img' => 'kebab.png',
=======
            'img' => "keb_icon.jpg",
>>>>>>> dcd565cb6b8ffb4893942ed08f17ca3f7a91505b
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }
}
