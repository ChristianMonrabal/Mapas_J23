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
            'name' => "Alonso's Cafe",
            'address' => "Av. Mare de Déu de Bellvitge, 86, 08907 L'Hospitalet de Llobregat, Barcelona",
            'latitude' => 41.3492394,
            'longitude' => 2.1049299,
            'description' => "Bar de tapas y Café",
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        
        Place::create([
            'name' => 'Ermita Sta Mª de Bellvitge',
            'address' => "Carrer de l'Ermita de Bellvitge, 6, 08907 L'Hospitalet de Llobregat, Barcelona",
            'latitude' => 41.3485316,
            'longitude' => 2.1083162,
            'description' => "Bar de tapas y Café",
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        
        Place::create([
            'name' => 'Parque de Bellvitge',
            'address' => "Carrer de l'Ermita de Bellvitge, 38, 08907 L'Hospitalet de Llobregat, Barcelona",
            'latitude' => 41.348399,
            'longitude' => 2.1085102,
            'description' => "Bar de tapas y Café",
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        
        Place::create([
            'name' => 'Centre Cultural Bellvitge-Gornal',
            'address' => "Plaça de la Cultura, 1, 08907 L'Hospitalet de Llobregat, Barcelona",
            'latitude' => 41.3512486,
            'longitude' => 2.109487,
            'description' => "Bar de tapas y Café",
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        
        Place::create([
            'name' => 'Pista de futsal',
            'address' => "Av. d'Amèrica, 117B, 08907 L'Hospitalet de Llobregat, Barcelona",
            'latitude' => 41.3523883,
            'longitude' => 2.1125246,
            'description' => "Bar de tapas y Café",
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        
        Place::create([
            'name' => 'Pirulo Bellvitge',
            'address' => "Av. d'Amèrica, 117B, 08907 L'Hospitalet de Llobregat, Barcelona",
            'latitude' => 41.3527157,
            'longitude' => 2.1117523,
            'description' => "Bar de tapas y Café",
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        
        Place::create([
            'name' => 'Petanca de Bellvitge',
            'address' => "08907 L'Hospitalet de Llobregat, Barcelona",
            'latitude' => 41.3534352,
            'longitude' => 2.112784,
            'description' => "Zona pública para jugar a la petanca",
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        Place::create([
            'name' => 'Xurreria La Confiança',
            'address' => "Av. d'Europa, 29, 08907 L'Hospitalet de Llobregat, Barcelona",
            'latitude' => 41.3527157,
            'longitude' => 2.1117523,
            'description' => "Churreria a muy buenos precios",
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        Place::create([
            'name' => 'Estación de tren Bellvitge',
            'address' => "Av. d'Amèrica, 08907 L'Hospitalet de Llobregat, Barcelona",
            'latitude' => 41.3549096,
            'longitude' => 2.1127199,
            'description' => "Estación de tren en Bellvitge",
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        Place::create([
            'name' => 'Polideportivo Municipal Bellvitge Sergio Manzano',
            'address' => "Av. Mare de Déu de Bellvitge, 7, 08907 L'Hospitalet de Llobregat, Barcelona",
            'latitude' => 41.3499005,
            'longitude' => 2.1047223,
            'description' => "Polideportivo AE Bellsport",
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        Place::create([
            'name' => 'Caprabo',
            'address' => "Av. Mare de Déu de Bellvitge, 40, 08907 L'Hospitalet de Llobregat, Barcelona",
            'latitude' => 41.3483958,
            'longitude' => 2.1077026,
            'description' => "Supermercado Caprabo",
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        Place::create([
            'name' => 'Tabacs Exp. nº 21',
            'address' => "Carrer de l'Ermita de Bellvitge, 47, Local A, 08907 L'Hospitalet de Llobregat, Barcelona",
            'latitude' => 41.3482729,
            'longitude' => 2.1078789,
            'description' => "Estanco nº 21",
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        Place::create([
            'name' => 'Consultas Externas Hospital de Bellvitge',
            'address' => "Carrer de la Feixa Llarga, s/n, 08907 L'Hospitalet de Llobregat, Barcelona",
            'latitude' => 41.347926,
            'longitude' => 2.1081551,
            'description' => "Hospital Bellvitge",
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        Place::create([
            'name' => 'BELLVITGE PITA KEBAB',
            'address' => "Passeig de Bellvitge, 08907 L'Hospitalet de Llobregat, Barcelona",
            'latitude' => 41.3478866,
            'longitude' => 2.1057738,
            'description' => "Kebab Bellvitge",
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        
    }
}
