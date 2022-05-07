<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Provider;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'category_id'   => Category::factory(),
            'provider_id'   => Provider::factory(),
            'name'          => $this->faker->unique()->word,
            'stock'         => $this->faker->numberBetween(1, 100),
            'image'         => null,
            'price'         => $this->faker->randomFloat(2, 0.01),
            'status'        => $this->faker->boolean,
            'code'          => $this->faker->bothify('###-#??#-?##??')
        ];
    }
}
