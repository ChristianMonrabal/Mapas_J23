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
            'latitude' => 41.34923884348558,
            'longitude' => 2.107504796086143,
            'description' => "Bar de tapas y Café",
            'img' => "alonso.jpg",
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        
        Place::create([
            'name' => 'Ermita Sta Mª de Bellvitge',
            'address' => "Carrer de l'Ermita de Bellvitge, 6, 08907 L'Hospitalet de Llobregat, Barcelona",
            'latitude' => 41.34740916452,
            'longitude' => 2.109722696085165,
            'description' => "Iglesia para orar al señor",
            'img' => "ermita.jpg",
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        
        Place::create([
            'name' => 'Parque de Bellvitge',
            'address' => "Carrer de l'Ermita de Bellvitge, 38, 08907 L'Hospitalet de Llobregat, Barcelona",
            'latitude' => 41.34839817704779,
            'longitude' => 2.1109675288613423,
            'description' => "Parque para que jueguen los niños",
            'img' => "parque.jpg",
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        
        Place::create([
            'name' => 'Centre Cultural Bellvitge-Gornal',
            'address' => "Plaça de la Cultura, 1, 08907 L'Hospitalet de Llobregat, Barcelona",
            'latitude' => 41.351247880486525,
            'longitude' => 2.1142504838519294,
            'description' => "Centro cultural abierto para la gente del barrio",
            'img' => "centrocultural.jpg",
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        
        Place::create([
            'name' => 'Pista de futsal',
            'address' => "Av. d'Amèrica, 117B, 08907 L'Hospitalet de Llobregat, Barcelona",
            'latitude' => 41.35238726459627,
            'longitude' => 2.113220925533215,
            'description' => "Cancha de baloncesto",
            'img' => "pistafutsal.jpg",
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        
        Place::create([
            'name' => 'Pirulo Bellvitge',
            'address' => "Av. d'Amèrica, 117B, 08907 L'Hospitalet de Llobregat, Barcelona",
            'latitude' => 41.3527150679557,
            'longitude' => 2.1132375666376957,
            'description' => "Centro de ocio para los niños",
            'img' => "pirulo.jpg",
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        
        Place::create([
            'name' => 'Petanca de Bellvitge',
            'address' => "08907 L'Hospitalet de Llobregat, Barcelona",
            'latitude' => 41.35349987069205,
            'longitude' => 2.1130038951058445,
            'description' => "Zona pública para jugar a la petanca",
            'img' => "petanca.jpg",
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        Place::create([
            'name' => 'Xurreria La Confiança',
            'address' => "Av. d'Europa, 29, 08907 L'Hospitalet de Llobregat, Barcelona",
            'latitude' => 41.353431167961254,
            'longitude' => 2.1137147960854787,
            'description' => "Churreria a muy buenos precios",
            'img' => "churreria.jpg",
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        Place::create([
            'name' => 'Estación de tren Bellvitge',
            'address' => "Av. d'Amèrica, 08907 L'Hospitalet de Llobregat, Barcelona",
            'latitude' => 41.354909067972386,
            'longitude' => 2.1152947960855433,
            'description' => "Estación de tren en Bellvitge",
            'img' => "trenBellvtg.jpg",
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        Place::create([
            'name' => 'Polideportivo Municipal Bellvitge Sergio Manzano',
            'address' => "Av. Mare de Déu de Bellvitge, 7, 08907 L'Hospitalet de Llobregat, Barcelona",
            'latitude' => 41.348389654509695,
            'longitude' => 2.105874236356942,
            'description' => "Polideportivo AE Bellsport",
            'created_at' => $now,
            'img' => "polideportivo.jpg",
            'updated_at' => $now,
        ]);

        Place::create([
            'name' => 'Caprabo',
            'address' => "Av. Mare de Déu de Bellvitge, 40, 08907 L'Hospitalet de Llobregat, Barcelona",
            'latitude' => 41.348384951515875,
            'longitude' => 2.107712728188918,
            'description' => "Supermercado Caprabo",
            'created_at' => $now,
            'img' => "caprabo.jpg",
            'updated_at' => $now,
        ]);

        Place::create([
            'name' => 'Tabacs Exp. nº 21',
            'address' => "Carrer de l'Ermita de Bellvitge, 47, Local A, 08907 L'Hospitalet de Llobregat, Barcelona",
            'latitude' => 41.34831688437959,
            'longitude' => 2.108160812095318,
            'description' => "Estanco nº 21",
            'created_at' => $now,
            'img' => "tabacs.jpg",
            'updated_at' => $now,
        ]);

        Place::create([
            'name' => 'Consultas Externas Hospital de Bellvitge',
            'address' => "Carrer de la Feixa Llarga, s/n, 08907 L'Hospitalet de Llobregat, Barcelona",
            'latitude' => 41.34469090223468,
            'longitude' => 2.1055756960012317,
            'description' => "Hospital Bellvitge",
            'created_at' => $now,
            'img' => "hospital.jpg",
            'updated_at' => $now,
        ]);

        Place::create([
            'name' => 'BELLVITGE PITA KEBAB',
            'address' => "Passeig de Bellvitge, 08907 L'Hospitalet de Llobregat, Barcelona",
            'latitude' => 41.34788100230696,
            'longitude' => 2.1083479830420533,
            'description' => "Kebab Bellvitge",
            'created_at' => $now,
            'img' => "kebab.jpg",
            'updated_at' => $now,
        ]);
        
        // Añadir la estación de Rambla Just Oliveras y puntos cercanos
        Place::create([
            'name' => 'Estación Rambla Just Oliveras',
            'address' => "Av. del Carrilet, 3, 08901 L'Hospitalet de Llobregat, Barcelona",
            'latitude' => 41.363807086947574,
            'longitude' => 2.0999359656497263,
            'description' => "Estación de metro L1 en el centro de L'Hospitalet",
            'img' => 'estacion_just_oliveras.jpg',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        
        Place::create([
            'name' => "Plaça Ajuntament de l'Hospitalet",
            'address' => "Plaça de l'Ajuntament, 08901 L'Hospitalet de Llobregat, Barcelona",
            'latitude' => 41.35975105395135, 
            'longitude' => 2.099698661507161,
            'description' => "Plaza con el edificio del Ayuntamiento de L'Hospitalet",
            'img' => 'plaza_ayuntamiento.jpg',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        
        Place::create([
            'name' => 'Café Bar La Parada',
            'address' => "Carrer Major, 3, 08901 L'Hospitalet de Llobregat, Barcelona",
            'latitude' => 41.35895333404509, 
            'longitude' => 2.0994733123083207,
            'description' => 'Café y bar tradicional cerca de la estación',
            'img' => 'cafe_parada.jpg',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        
        Place::create([
            'name' => 'CeX',
            'address' => "Carrer de Sant Rafael, 08901 L'Hospitalet de Llobregat, Barcelona",
            'latitude' => 41.36055387691393, 
            'longitude' => 2.1019315291646445,
            'description' => 'Pequeño parque con zona de juegos infantiles',
            'img' => 'parque_remonta.jpg',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        
        Place::create([
            'name' => 'Centro Comercial La Farga',
            'address' => "Av. d'Isabel la Catòlica, 4, 08901 L'Hospitalet de Llobregat, Barcelona",
            'latitude' => 41.362039614369955, 
            'longitude' => 2.104877606872715,
            'description' => "Centro comercial y de eventos emblemático de L'Hospitalet",
            'img' => 'la_farga.jpg',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        
    }
}
