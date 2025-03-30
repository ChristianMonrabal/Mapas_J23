<?php

namespace Database\Seeders;

use App\Models\Group;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class GroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        Group::create([
            'name'         => 'Exploradores de L\'Hospitalet',
            'codigo'       => 'HOSP1',
            'creador'      => 1, // ID de un usuario existente
            'max_miembros' => 4, // Capacidad máxima del grupo (entre 2 y 4)
            'game_started' => false, // El juego no ha iniciado
            'created_at'   => $now,
            'updated_at'   => $now,
        ]);

        Group::create([
            'name'         => 'Caminantes de La Farga',
            'codigo'       => 'FARG2',
            'creador'      => 1,
            'max_miembros' => 4,
            'game_started' => false,
            'created_at'   => $now,
            'updated_at'   => $now,
        ]);

        Group::create([
            'name'         => 'Aventureros del Metro',
            'codigo'       => 'METR3',
            'creador'      => 2, // Otro usuario
            'max_miembros' => 4,
            'game_started' => false,
            'created_at'   => $now,
            'updated_at'   => $now,
        ]);

        Group::create([
            'name'         => 'Rambla Team',
            'codigo'       => 'RAMB4',
            'creador'      => 2,
            'max_miembros' => 4,
            'game_started' => false,
            'created_at'   => $now,
            'updated_at'   => $now,
        ]);
    }
}
