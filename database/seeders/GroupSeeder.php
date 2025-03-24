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
            'name'       => 'Group 1',
            'codigo'       => 'GRP1',
            'creador'      => 1, // ID de un usuario existente
            'gymkhana_id'  => 1, // ID de una gimcana existente
            'max_miembros' => 4, // Capacidad máxima del grupo (entre 2 y 4)
            'created_at'   => $now,
            'updated_at'   => $now,
        ]);

        Group::create([
            'name'       => 'Group 2',
            'codigo'       => 'GRP2',
            'creador'      => 1,
            'gymkhana_id'  => 1,
            'max_miembros' => 4,
            'created_at'   => $now,
            'updated_at'   => $now,
        ]);

        Group::create([
            'name'       => 'Group 3',
            'codigo'       => 'GRP3',
            'creador'      => 2, // Otro usuario
            'gymkhana_id'  => 2,
            'max_miembros' => 4,
            'created_at'   => $now,
            'updated_at'   => $now,
        ]);

        Group::create([
            'name'       => 'Group 4',
            'codigo'       => 'GRP4',
            'creador'      => 2,
            'gymkhana_id'  => 2,
            'max_miembros' => 4,
            'created_at'   => $now,
            'updated_at'   => $now,
        ]);
    }
}
