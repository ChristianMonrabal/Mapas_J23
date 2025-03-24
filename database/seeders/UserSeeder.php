<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

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
            'email_verified_at' => $now,
            'password' => bcrypt('qweQWE123'),
            'role_id' => 1,
            'remember_token' => Str::random(10),
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        
        User::create([
            'name' => 'Marc',
            'email' => 'marc@gmail.com',
            'email_verified_at' => $now,
            'password' => bcrypt('qweQWE123'),
            'role_id' => 1,
            'remember_token' => Str::random(10),
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        
        User::create([
            'name' => 'Christian',
            'email' => 'christian@gmail.com',
            'email_verified_at' => $now,
            'password' => bcrypt('qweQWE123'),
            'role_id' => 2,
            'remember_token' => Str::random(10),
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        User::create([
            'name' => 'Daniel',
            'email' => 'daniel@gmail.com',
            'email_verified_at' => $now,
            'password' => bcrypt('qweQWE123'),
            'role_id' => 2,
            'remember_token' => Str::random(10),
            'created_at' => $now,
            'updated_at' => $now,
        ]);

    }
}