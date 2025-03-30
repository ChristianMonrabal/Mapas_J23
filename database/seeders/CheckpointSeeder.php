<?php

namespace Database\Seeders;

use App\Models\Checkpoint;
use App\Models\Gymkhana;
use App\Models\Place;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CheckpointSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now =  Carbon::now();

        // Checkpoints para las gymkhanas existentes
        Checkpoint::create([
            'gymkhana_id' => 1,
            'place_id' => 1,
            'pista' => 'Donde se toma café el 33?',
            'completed' => false,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        Checkpoint::create([
            'gymkhana_id' => 1,
            'place_id' => 2,
            'pista' => 'Donde se reza?',
            'completed' => false,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        Checkpoint::create([
            'gymkhana_id' => 1,
            'place_id' => 3,
            'pista' => 'Donde se puede tomar la fresca?',
            'completed' => false,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        Checkpoint::create([
            'gymkhana_id' => 1,
            'place_id' => 4,
            'pista' => 'Donde se puede pasear por la arena?',
            'completed' => false,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        
        Checkpoint::create([
            'gymkhana_id' => 1,
            'place_id' => 5,
            'pista' => 'Donde se puede pasear por la montaña?',
            'completed' => false,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        
        Checkpoint::create([
            'gymkhana_id' => 1,
            'place_id' => 6,
            'pista' => 'Donde se puede pasear por las calles?',
            'completed' => false,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        
        Checkpoint::create([
            'gymkhana_id' => 2,
            'place_id' => 2,
            'pista' => 'Donde se puede rezar en el centro?',
            'completed' => false,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        
        Checkpoint::create([
            'gymkhana_id' => 2,
            'place_id' => 3,
            'pista' => 'Donde se puede tomar la fresca en el centro?',
            'completed' => false,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        
        Checkpoint::create([
            'gymkhana_id' => 2,
            'place_id' => 7,
            'pista' => 'Donde vive el alcalde?',
            'completed' => false,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        
        Checkpoint::create([
            'gymkhana_id' => 2,
            'place_id' => 8,
            'pista' => 'Donde hay un anfiteatro en el centro?',
            'completed' => false,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // Obtener la gymkhana "Ruta L'Hospitalet"
        $rutaHospitalet = Gymkhana::where('name', 'Ruta L\'Hospitalet')->first();
        $gymkhanaId = $rutaHospitalet->id;

        // Obtener los lugares por nombre
        $estacionRambla = Place::where('name', 'Estación Rambla Just Oliveras')->first();
        $ayuntamiento = Place::where('name', 'Plaça Ajuntament de l\'Hospitalet')->first();
        $cafeLaParada = Place::where('name', 'Café Bar La Parada')->first();
        $parqueRemonta = Place::where('name', 'Parque de la Remonta')->first();
        $centroLaFarga = Place::where('name', 'Centro Comercial La Farga')->first();

        // Checkpoint 1: Comenzar en la estación
        Checkpoint::create([
            'gymkhana_id' => $gymkhanaId,
            'place_id' => $estacionRambla->id,
            'pista' => '¡Búscala en la línea roja del metro! Es la estación RAMBLA JUST OLIVERAS. Verás las letras rojas de "METRO" en la entrada principal. Está donde se cruzan Rambla Just Oliveras y Av. del Carrilet.',
            'completed' => false,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // Checkpoint 2: Ir a la Plaza del Ayuntamiento
        Checkpoint::create([
            'gymkhana_id' => $gymkhanaId,
            'place_id' => $ayuntamiento->id,
            'pista' => 'Desde la estación, camina 100m por la Rambla hasta encontrar una plaza grande con banderas en la fachada del edificio principal. Es el AYUNTAMIENTO DE L\'HOSPITALET. Verás la fachada color crema con el escudo de la ciudad.',
            'completed' => false,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // Checkpoint 3: Tomar un descanso en el Café La Parada
        Checkpoint::create([
            'gymkhana_id' => $gymkhanaId,
            'place_id' => $cafeLaParada->id,
            'pista' => 'Desde el Ayuntamiento, sube por CARRER MAJOR. A unos 80m encontrarás el CAFÉ BAR LA PARADA. Tiene una terraza con sombrillas y una entrada con toldo rojo. ¡Es el bar más antiguo de la zona!',
            'completed' => false,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // Checkpoint 4: Visitar el Parque de la Remonta
        Checkpoint::create([
            'gymkhana_id' => $gymkhanaId,
            'place_id' => $parqueRemonta->id,
            'pista' => 'Sigue caminando hacia arriba por Sant Rafael. A menos de 150m del café encontrarás el PARQUE DE LA REMONTA. Reconocerás la entrada por sus grandes árboles y el área de juegos infantiles con columpios rojos y amarillos.',
            'completed' => false,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // Checkpoint 5: Finalizar en el Centro Comercial La Farga
        Checkpoint::create([
            'gymkhana_id' => $gymkhanaId,
            'place_id' => $centroLaFarga->id,
            'pista' => 'Para terminar, dirígete al CENTRO COMERCIAL LA FARGA en Av. Isabel la Católica. Es un edificio grande con un letrero gigante de "LA FARGA" en la entrada. Tiene un Mercadona y muchas tiendas. ¡No tiene pérdida!',
            'completed' => false,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }
}
