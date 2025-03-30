<?php

namespace Database\Seeders;

use App\Models\GymkhanaProgress;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GymkhanaProgressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        // Encontrar los grupos y checkpoints por nombre
        $exploradores = DB::table('groups')->where('name', 'Exploradores de L\'Hospitalet')->first();
        $caminantes = DB::table('groups')->where('name', 'Caminantes de La Farga')->first();
        $aventureros = DB::table('groups')->where('name', 'Aventureros del Metro')->first();
        
        // Obtener la gymkhana "Ruta L'Hospitalet"
        $rutaHospitalet = DB::table('gymkhanas')->where('name', 'Ruta L\'Hospitalet')->first();
        
        // Encontrar los checkpoints de la gymkhana de L'Hospitalet
        $checkpoints = DB::table('checkpoints')->where('gymkhana_id', $rutaHospitalet->id)->get();
        $checkpoint1 = $checkpoints[0];
        $checkpoint2 = $checkpoints[1];
        $checkpoint3 = $checkpoints[2];
        
        // Obtener los usuarios de cada grupo
        $exploradoresUsers = DB::table('group_users')
            ->where('group_id', $exploradores->id)
            ->get();
            
        $caminantesUsers = DB::table('group_users')
            ->where('group_id', $caminantes->id)
            ->get();
            
        $aventurerosUsers = DB::table('group_users')
            ->where('group_id', $aventureros->id)
            ->get();

        // Progreso para el grupo "Exploradores de L'Hospitalet" en la gymkhana de L'Hospitalet
        // Checkpoint 1 (Estación) - Completado
        GymkhanaProgress::create([
            'group_users_id' => $exploradoresUsers[0]->id,
            'checkpoint_id' => $checkpoint1->id,
            'completed' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // Checkpoint 2 (Ayuntamiento) - Completado
        GymkhanaProgress::create([
            'group_users_id' => $exploradoresUsers[0]->id,
            'checkpoint_id' => $checkpoint2->id,
            'completed' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // Checkpoint 3 (Café) - En progreso
        GymkhanaProgress::create([
            'group_users_id' => $exploradoresUsers[0]->id,
            'checkpoint_id' => $checkpoint3->id,
            'completed' => false,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // Progreso para el grupo "Caminantes de La Farga" en la gymkhana de L'Hospitalet
        // Solo han completado el primer checkpoint
        GymkhanaProgress::create([
            'group_users_id' => $caminantesUsers[0]->id,
            'checkpoint_id' => $checkpoint1->id,
            'completed' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        GymkhanaProgress::create([
            'group_users_id' => $caminantesUsers[0]->id,
            'checkpoint_id' => $checkpoint2->id,
            'completed' => false,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // Progreso para el grupo "Aventureros del Metro" en una gymkhana anterior
        $otroCheckpoint = DB::table('checkpoints')->where('gymkhana_id', 1)->first();
        
        GymkhanaProgress::create([
            'group_users_id' => $aventurerosUsers[0]->id,
            'checkpoint_id' => $otroCheckpoint->id,
            'completed' => false,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }
}
