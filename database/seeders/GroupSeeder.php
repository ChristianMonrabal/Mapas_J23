<?php

namespace Database\Seeders;

use App\Models\Group;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
            'name' => 'Group 1',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        Group::create([
            'name' => 'Group 2',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        Group::create([
            'name' => 'Group 3',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        Group::create([
            'name' => 'Group 4',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

    }
}
