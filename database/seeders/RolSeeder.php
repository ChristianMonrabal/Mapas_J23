<?php

namespace Database\Seeders;

use App\Models\Role;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        Role::create([
            'name' => 'client',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        Role::create([
            'name' => 'administrator',
            'created_at' => $now,
            'updated_at' => $now,
        ]);  
    }
}
