<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        $categories = [
            ['name' => 'Oli dan Pelumas'],
            ['name' => 'Aki dan Kelistrikan'],
            ['name' => 'Ban dan Velg'],
            ['name' => 'Komponen Mesin'],
            ['name' => 'Aksesori'],

        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
