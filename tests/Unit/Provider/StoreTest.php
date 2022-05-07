<?php

namespace Tests\Unit\Provider;

use App\Models\Provider;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class StoreTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function test_store(){
        $user = User::factory()->create();
        $token = $user->createToken('default')->plainTextToken;
        $data = [
            'name'  => $this->faker->name,
            'email' => $this->faker->companyEmail
        ];
        $this->post(route('provider.store'), $data, [
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer '.$token
        ])->assertOk()
            ->assertJson([
                'status' => 'success',
                'provider' => ['name' => $data['name']]
            ]);
        $this->assertDatabaseHas('providers', $data);
    }

    public function test_invalid_data(){
        $user = User::factory()->create();
        $token = $user->createToken('default')->plainTextToken;
        $this->post(route('provider.store'), [], [
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer '.$token
        ])->assertStatus(400)
            ->assertJson(['status' => 'error'])
            ->assertJsonCount(2, 'errors');
    }

    public function test_name_exists(){
        $provider = Provider::factory()->create();
        $user = User::factory()->create();
        $token = $user->createToken('default')->plainTextToken;
        $data = ['name'  => $provider->name];
        $this->post(route('provider.store'), $data, [
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer '.$token
        ])->assertStatus(400)
            ->assertJson(['status' => 'error'])
            ->assertJsonCount(1, 'errors.name');
    }
}
