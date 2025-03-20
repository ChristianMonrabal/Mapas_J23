<?php

namespace Database\Seeders;

use App\Models\GymkhanaProgress;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GymkhanaProgressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        GymkhanaProgress::create([
            'group_users_id' => 1,
            'checkpoint_id' => 1,
            'completed' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        
    }
}
