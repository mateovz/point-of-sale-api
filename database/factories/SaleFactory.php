<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SaleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'client_id'     => Client::factory(),
            'user_id'       => User::factory(),
            'sale_date'     => $this->faker->time(),
            'tax'           => $this->faker->randomFloat(2, 0.01, 20),
            'total'         => $this->faker->randomFloat(2, 0.01, 50),
            'status'        => $this->faker->boolean
        ];
    }
}
