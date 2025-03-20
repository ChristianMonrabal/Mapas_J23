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

        // Asignación de usuarios a grupos
        $group1 = Group::where('name', 'Group 1')->first();
        $group2 = Group::where('name', 'Group 2')->first();

        $user1 = User::where('email', 'polmarc@gmail.com')->first();
        $user2 = User::where('email', 'marc@gmail.com')->first();
        $user3 = User::where('email', 'christian@gmail.com')->first();
        $user4 = User::where('email', 'daniel@gmail.com')->first();

        $group1->users()->attach([$user1->id, $user2->id]);
        $group2->users()->attach([$user3->id, $user4->id]);
        
    }
}
