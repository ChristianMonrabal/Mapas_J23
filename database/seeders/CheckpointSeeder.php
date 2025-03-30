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
        // Checkpoint::create([
        //     'gymkhana_id' => 1,
        //     'place_id' => 1,
        //     'pista' => 'Donde se toma café el 33?',
        //     'completed' => false,
        //     'created_at' => $now,
        //     'updated_at' => $now,
        // ]);

        // Checkpoint::create([
        //     'gymkhana_id' => 1,
        //     'place_id' => 2,
        //     'pista' => 'Donde se reza?',
        //     'completed' => false,
        //     'created_at' => $now,
        //     'updated_at' => $now,
        // ]);

        // Checkpoint::create([
        //     'gymkhana_id' => 1,
        //     'place_id' => 3,
        //     'pista' => 'Donde se hacen los conciertos de la BMF?',
        //     'completed' => false,
        //     'created_at' => $now,
        //     'updated_at' => $now,
        // ]);

        // Checkpoint::create([
        //     'gymkhana_id' => 1,
        //     'place_id' => 4,
        //     'pista' => 'Un sitio con mucha cultura en el centro...',
        //     'completed' => false,
        //     'created_at' => $now,
        //     'updated_at' => $now,
        // ]);

        // Checkpoint::create([
        //     'gymkhana_id' => 1,
        //     'place_id' => 6,
        //     'pista' => 'La primera palabra de este sitio es un chiste que viene a hacernos un señor mayor de Bellvitge',
        //     'completed' => false,
        //     'created_at' => $now,
        //     'updated_at' => $now,
        // ]);

        // Checkpoint::create([
        //     'gymkhana_id' => 1,
        //     'place_id' => 7,
        //     'pista' => 'Que pone aquí?: PE_A__A',
        //     'completed' => false,
        //     'created_at' => $now,
        //     'updated_at' => $now,
        // ]);

        // Checkpoint::create([
        //     'gymkhana_id' => 1,
        //     'place_id' => 8,
        //     'pista' => 'Unos xurros por la mañana te dan confianza',
        //     'completed' => false,
        //     'created_at' => $now,
        //     'updated_at' => $now,
        // ]);

        // Checkpoint::create([
        //     'gymkhana_id' => 1,
        //     'place_id' => 9,
        //     'pista' => 'Como se va a la playa?',
        //     'created_at' => $now,
        //     'completed' => false,
        //     'updated_at' => $now,
        // ]);
        
        // Checkpoint::create([
        //     'gymkhana_id' => 2,
        //     'place_id' => 2,
        //     'pista' => 'Donde se puede rezar en el centro?',
        //     'completed' => false,
        //     'created_at' => $now,
        //     'updated_at' => $now,
        // ]);
        
        // Checkpoint::create([
        //     'gymkhana_id' => 2,
        //     'place_id' => 3,
        //     'pista' => 'Donde se puede tomar la fresca en el centro?',
        //     'completed' => false,
        //     'created_at' => $now,
        //     'updated_at' => $now,
        // ]);
        
        // Checkpoint::create([
        //     'gymkhana_id' => 2,
        //     'place_id' => 7,
        //     'pista' => 'Donde vive el alcalde?',
        //     'completed' => false,
        //     'created_at' => $now,
        //     'updated_at' => $now,
        // ]);
        
        // Checkpoint::create([
        //     'gymkhana_id' => 2,
        //     'place_id' => 8,
        //     'pista' => 'Donde hay un anfiteatro en el centro?',
        //     'completed' => false,
        //     'created_at' => $now,
        //     'updated_at' => $now,
        // ]);

        // Obtener la gymkhana "Ruta L'Hospitalet"
        $rutaHospitalet = Gymkhana::where('name', "Ruta L'Hospitalet")->first();
        $gymkhanaId = $rutaHospitalet->id;

        // Obtener los lugares por nombre
        $estacionRambla = Place::where('name', 'Estación Rambla Just Oliveras')->first();
        $ayuntamiento = Place::where('name', "Plaça Ajuntament de l'Hospitalet")->first();
        $cafeLaParada = Place::where('name', 'Café Bar La Parada')->first();
        $parqueRemonta = Place::where('name', 'Cex')->first();
        $centroLaFarga = Place::where('name', 'Centro Comercial La Farga')->first();

        // Checkpoint 1: Comenzar en la estación
        Checkpoint::create([
            'gymkhana_id' => $gymkhanaId,
            'place_id' => $estacionRambla->id,
            'pista' => 'Donde está el ayuntamiento del municipio donde estamos?',
            'completed' => false,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // Checkpoint 2: Ir a la Plaza del Ayuntamiento
        Checkpoint::create([
            'gymkhana_id' => $gymkhanaId,
            'place_id' => $ayuntamiento->id,
            'pista' => 'Aquí cerca hay una "parada" para tomarse un café',
            'completed' => false,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // Checkpoint 3: Tomar un descanso en el Café La Parada
        Checkpoint::create([
            'gymkhana_id' => $gymkhanaId,
            'place_id' => $cafeLaParada->id,
            'pista' => 'Un parque para los niños, lleno de arena. Se entra por un portal enorme, de acero negro y está entre dos farolas',
            'completed' => false,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // Checkpoint 4: Visitar el Parque de la Remonta
        Checkpoint::create([
            'gymkhana_id' => $gymkhanaId,
            'place_id' => $parqueRemonta->id,
            'pista' => 'Y si queremos ir a comprar ropa, comida o ver una película, a donde vamos?',
            'completed' => false,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // Checkpoint 5: Finalizar en el Centro Comercial La Farga
        Checkpoint::create([
            'gymkhana_id' => $gymkhanaId,
            'place_id' => $centroLaFarga->id,
            'pista' => 'Felicidades, has encontrado la última pista!!',
            'completed' => false,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }
}
