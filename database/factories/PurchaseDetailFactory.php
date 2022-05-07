<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Purchase;
use Illuminate\Database\Eloquent\Factories\Factory;

class PurchaseDetailFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'purchase_id'   => Purchase::factory(),
            'product_id'    => Product::factory(),
            'quantity'      => $this->faker->numberBetween(1, 20),
            'price'         => $this->faker->randomFloat(2, 0.01, 50)
        ];
    }
}
