<?php

namespace Tests\Unit\Provider;

use App\Models\Provider;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function test_update(){
        $provider = Provider::factory()->create();
        $user = User::factory()->create();
        $token = $user->createToken('default')->plainTextToken;
        $data = [
            'name'  => $provider->name,
            'email' => $this->faker->companyEmail,
            'phone' => $this->faker->numerify('+## #########')
        ];
        $this->put(route('provider.update', ['provider' => $provider->id]), $data, [
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer '.$token
        ])->assertOk()
            ->assertJson([
                'status'    => 'success',
                'provider'  => ['id' => $provider->id]
            ]);
        $this->assertDatabaseHas('providers', $data);
    }

    public function test_invalid_data(){
        $provider = Provider::factory()->create();
        $token = User::factory()->create()
            ->createToken('default')->plainTextToken;
        $data = ['email' => null];
        $this->put(route('provider.update', ['provider' => $provider->id]), $data, [
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer '.$token
        ])->assertStatus(400)
            ->assertJson(['status' => 'error'])
            ->assertJsonStructure(['errors' => ['email']]);
    }

    public function test_name_exists(){
        $provider1 = Provider::factory()->create();
        $provider2 = Provider::factory()->create();
        $user = User::factory()->create();
        $token = $user->createToken('default')->plainTextToken;
        $data = [
            'name'  => $provider1->name,
            'email' => $this->faker->companyEmail,
            'phone' => $this->faker->numerify('+## #########')
        ];
        $this->put(route('provider.update', ['provider' => $provider2->id]), $data, [
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer '.$token
        ])->assertStatus(400)
            ->assertJson(['status'  => 'error'])
            ->assertJsonCount(1, 'errors.name');
    }

    public function test_provider_does_not_exists(){
        $user = User::factory()->create();
        $token = $user->createToken('default')->plainTextToken;
        $data = [
            'name'  => $this->faker->name,
            'email' => $this->faker->companyEmail,
            'phone' => $this->faker->numerify('+## #########')
        ];
        $this->put(route('provider.update', ['provider' => random_int(10, 20)]), $data, [
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer '.$token
        ])->assertStatus(400)
            ->assertJson(['status' => 'error']);
    }
}
