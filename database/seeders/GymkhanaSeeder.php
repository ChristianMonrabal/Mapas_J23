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
        
        // Nueva gymkhana para el centro de L'Hospitalet
        Gymkhana::create([
            'name' => 'Ruta L\'Hospitalet',
            'description' => 'Descubre el centro histórico de L\'Hospitalet partiendo desde la estación de Rambla Just Oliveras',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }
}
