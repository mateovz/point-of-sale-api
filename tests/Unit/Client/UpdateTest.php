<?php

namespace Tests\Unit\Client;

use App\Models\Client;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function test_update(){
        $client = Client::factory()->create();
        $token = User::factory()->create()
            ->createToken('default')->plainTextToken;
        $data = [
            'name'              => $this->faker->name,
            'identification'    => $this->faker->bothify('??####'),
            'email'             => $this->faker->email,
        ];
        $this->put(route('client.update', ['client' => $client->id]), $data, [
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer '.$token
        ])->assertOk()
            ->assertJson([
                'status'    => 'success',
                'client'  => ['id' => $client->id]
            ]);
        $this->assertDatabaseHas('clients', $data);
    }

    public function test_invalid_data(){
        $client = Client::factory()->create();
        $token = User::factory()->create()
            ->createToken('default')->plainTextToken;
        $this->put(route('client.update', ['client' => $client->id]), [], [
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer '.$token
        ])->assertStatus(400)
            ->assertJson(['status' => 'error'])
            ->assertJsonStructure(['errors' => ['email']]);
    }

    public function test_name_exists(){
        $client1 = Client::factory()->create();
        $client2 = Client::factory()->create();
        $token = User::factory()->create()
            ->createToken('default')->plainTextToken;
        $data = [
            'identification'    => ['identification' => $client1->identification],
            'ruc'               => ['ruc' => $client1->ruc],
            'email'             => ['email' => $client1->email]
        ];
        foreach ($data as $key => $value) {
            $this->put(route('client.update', ['client' => $client2->id]), $value, [
                    'Accept'        => 'application/json',
                    'Authorization' => 'Bearer '.$token
                ])->assertStatus(400)
                    ->assertJson(['status'  => 'error'])
                    ->assertJsonStructure(['errors' => [$key]]);
        }
    }

    public function test_client_does_not_exists(){
        $token = User::factory()->create()
            ->createToken('default')->plainTextToken;
        $data = [
            'name'              => $this->faker->name,
            'identification'    => $this->faker->bothify('??####'),
            'email'             => $this->faker->email,
        ];
        $this->put(route('client.update', ['client' => random_int(10, 20)]), $data, [
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer '.$token
        ])->assertStatus(400)
            ->assertJson(['status' => 'error'])
            ->assertJsonStructure(['errors' => ['client']]);
    }
}
