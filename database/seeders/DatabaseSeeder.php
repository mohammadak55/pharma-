<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $user1 = User::create([
            'name' => 'main warehouse Manager',
            'phone' => '0956497719',
            'password' => bcrypt('password'),
            'role' => "warehouse" ,
        ]);

        $defaultWarehouse = Warehouse::create([
            'name' => "main warehouse",
            "location" => "damascus",
            "user_id" => $user1->id,
            // Add other warehouse details as needed
        ]);



        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
