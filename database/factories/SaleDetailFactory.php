<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Sale;
use Illuminate\Database\Eloquent\Factories\Factory;

class SaleDetailFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'sale_id'       => Sale::factory(),
            'product_id'    => Product::factory(),
            'quantity'      => $this->faker->numberBetween(1, 10),
            'price'         => $this->faker->randomFloat(2, 0.01, 10),
            'discount'      => $this->faker->randomFloat(2, 0, 10)
        ];
    }
}
