<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $faker = \Faker\Factory::create();
        foreach (range(1,10) as $item) {
            Product::create([
             'product_name' => $faker->word(20),
             'quantity' => $faker->randomDigit(),
             'price' => $faker->numberBetween($min = 1000, $max = 50000)
            ]);
        }
    }
}
