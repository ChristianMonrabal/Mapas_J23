<?php

namespace Database\Seeders;

use App\Models\Group;
use App\Models\GroupUser;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GroupUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $now = Carbon::now();

        // Asignación de usuarios a grupos
        $group1 = Group::where('name', 'Exploradores de L\'Hospitalet')->first();
        $group2 = Group::where('name', 'Caminantes de La Farga')->first();
        $group3 = Group::where('name', 'Aventureros del Metro')->first();
        $group4 = Group::where('name', 'Rambla Team')->first();

        $user1 = User::where('email', 'polmarc@gmail.com')->first();
        $user2 = User::where('email', 'marc@gmail.com')->first();
        $user3 = User::where('email', 'christian@gmail.com')->first();
        $user4 = User::where('email', 'daniel@gmail.com')->first();

        // Grupo 1: Exploradores de L'Hospitalet
        $group1->users()->attach([
            $user1->id => ['completed' => false, 'created_at' => $now, 'updated_at' => $now],
            $user2->id => ['completed' => false, 'created_at' => $now, 'updated_at' => $now]
        ]);
        
        // Grupo 2: Caminantes de La Farga
        $group2->users()->attach([
            $user3->id => ['completed' => false, 'created_at' => $now, 'updated_at' => $now],
            $user4->id => ['completed' => false, 'created_at' => $now, 'updated_at' => $now]
        ]);
        
        // Grupo 3: Aventureros del Metro
        $group3->users()->attach([
            $user1->id => ['completed' => false, 'created_at' => $now, 'updated_at' => $now],
            $user3->id => ['completed' => false, 'created_at' => $now, 'updated_at' => $now]
        ]);
        
        // Grupo 4: Rambla Team
        $group4->users()->attach([
            $user2->id => ['completed' => false, 'created_at' => $now, 'updated_at' => $now],
            $user4->id => ['completed' => false, 'created_at' => $now, 'updated_at' => $now]
        ]);
    }
}
