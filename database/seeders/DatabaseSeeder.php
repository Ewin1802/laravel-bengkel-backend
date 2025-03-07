<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        \App\Models\User::factory()->create([
            'name' => 'MJM Admin',
            'email' => 'mjm@admin.com',
            'password' => Hash::make('98764321'),
            'role' => 'admin',
        ]);

        \App\Models\User::factory()->create([
            'name' => 'MJM Staf',
            'email' => 'mjm@staf.com',
            'password' => Hash::make('11111111'),
            'role' => 'staff',
        ]);

        $this->call([
            CategorySeeder::class,
            // ProductSeeder::class,
            // DiscountSeeder::class,
        ]);
    }
}
