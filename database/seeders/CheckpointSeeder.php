<?php

namespace Database\Seeders;

use App\Models\Checkpoint;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CheckpointSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now =  Carbon::now();

        Checkpoint::create([
            'gymkhana_id' => 1,
            'place_id' => 1,
            'pista' => 'Donde se toma café el 33?',
            'completed' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        Checkpoint::create([
            'gymkhana_id' => 1,
            'place_id' => 2,
            'pista' => 'Donde se reza?',
            'completed' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        Checkpoint::create([
            'gymkhana_id' => 1,
            'place_id' => 3,
            'pista' => 'Donde se hacen los conciertos de la BMF?',
            'completed' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        Checkpoint::create([
            'gymkhana_id' => 1,
            'place_id' => 4,
            'pista' => 'Un sitio con mucha cultura en el centro...',
            'completed' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        Checkpoint::create([
            'gymkhana_id' => 1,
            'place_id' => 6,
            'pista' => 'La primera palabra de este sitio es un chiste que viene a hacernos un señor mayor de Bellvitge',
            'completed' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        Checkpoint::create([
            'gymkhana_id' => 1,
            'place_id' => 7,
            'pista' => 'Que pone aquí?: PE_A__A',
            'completed' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        Checkpoint::create([
            'gymkhana_id' => 1,
            'place_id' => 8,
            'pista' => 'Unos xurros por la mañana te dan confianza',
            'completed' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        Checkpoint::create([
            'gymkhana_id' => 1,
            'place_id' => 9,
            'pista' => 'Como se va a la playa?',
            'created_at' => $now,
            'completed' => true,
            'updated_at' => $now,
        ]);
    }
}
