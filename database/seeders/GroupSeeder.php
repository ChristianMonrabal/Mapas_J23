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
<<<<<<< HEAD
            'creador'      => 1, // ID de un usuario existente
            'max_miembros' => 4, // Capacidad máxima del grupo (entre 2 y 4)
=======
            'creador'      => 1,
            'max_miembros' => 4,
>>>>>>> dcd565cb6b8ffb4893942ed08f17ca3f7a91505b
            'created_at'   => $now,
            'updated_at'   => $now,
        ]);

        Group::create([
            'name'       => 'Group 2',
            'codigo'       => 'GRP2',
            'creador'      => 1,
            'max_miembros' => 4,
            'created_at'   => $now,
            'updated_at'   => $now,
        ]);

        Group::create([
            'name'       => 'Group 3',
            'codigo'       => 'GRP3',
<<<<<<< HEAD
            'creador'      => 2, // Otro usuario
=======
            'creador'      => 2,
>>>>>>> dcd565cb6b8ffb4893942ed08f17ca3f7a91505b
            'max_miembros' => 4,
            'created_at'   => $now,
            'updated_at'   => $now,
        ]);

        Group::create([
            'name'       => 'Group 4',
            'codigo'       => 'GRP4',
            'creador'      => 2,
            'max_miembros' => 4,
            'created_at'   => $now,
            'updated_at'   => $now,
        ]);
    }
}
