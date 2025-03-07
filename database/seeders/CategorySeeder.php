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
        // $categories = [
        //     ['name' => 'Makanan'],
        //     ['name' => 'Bestseller'],
        //     ['name' => 'Regalseries'],
        //     ['name' => 'Noncoffee'],
        //     ['name' => 'Americanoseries'],
        //     ['name' => 'Latteseries'],
        //     ['name' => 'Teaseries'],
        //     ['name' => 'Hotseries'],
        //     ['name' => 'Tradisionalseries'],
        //     ['name' => 'Manualbrew'],
        //     ['name' => 'Softdrink'],
        // ];
        $categories = [
            ['name' => 'Oli dan Pelumas'],
            ['name' => 'Aki dan Kelistrikan'],
            ['name' => 'Ban dan Velg'],
            ['name' => 'Aksesori'],

        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
