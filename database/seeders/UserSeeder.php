<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        User::create([
            'name' => 'Pol Marc',
            'email' => 'polmarc@gmail.com',
            'password' => bcrypt('qweQWE123'),
            'role_id' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        
        User::create([
            'name' => 'Marc',
            'email' => 'marc@gmail.com',
            'password' => bcrypt('qweQWE123'),
            'role_id' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        
        User::create([
            'name' => 'Christian',
            'email' => 'christian@gmail.com',
            'password' => bcrypt('qweQWE123'),
            'role_id' => 2,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        User::create([
            'name' => 'Daniel',
            'email' => 'daniel@gmail.com',
            'password' => bcrypt('qweQWE123'),
            'role_id' => 2,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

    }
}
