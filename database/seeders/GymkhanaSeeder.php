<?php

namespace Database\Seeders;

use App\Models\Gymkhana;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GymkhanaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        Gymkhana::create([
            'name' => 'Travesía Bellvitge',
            'description' => 'Ruta turística para descubrir nuevos caminos por Bellvitge',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        Gymkhana::create([
            'name' => 'Travesía',
            'description' => 'Ruta turística para descubrir nuevos caminos por Bellvitge',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }
}
