<?php

namespace Database\Factories;

use App\Models\Provider;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PurchaseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'provider_id'   => Provider::factory(),
            'user_id'       => User::factory(),
            'purchase_date' => $this->faker->time(),
            'tax'           => $this->faker->randomFloat(2, 1, 20),
            'total'         => $this->faker->randomFloat(2, 0.01),
            'status'        => $this->faker->boolean,
            'file'          => null
        ];
    }
}
