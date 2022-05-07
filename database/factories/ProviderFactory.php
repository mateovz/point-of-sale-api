<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProviderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name'      => $this->faker->unique()->name,
            'email'     => $this->faker->unique()->companyEmail,
            'address'   => $this->faker->address,
            'phone'     => $this->faker->phoneNumber,
            'ruc'       => $this->faker->unique()->numberBetween(1000, 9999)
        ];
    }
}
