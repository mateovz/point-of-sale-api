<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ClientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name'              => $this->faker->name,
            'identification'    => $this->faker->bothify('?#######'),
            'ruc'               => $this->faker->numerify('#########'),
            'email'             => $this->faker->email,
            'address'           => $this->faker->address
        ];
    }
}
