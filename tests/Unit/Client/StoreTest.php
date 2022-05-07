<?php

namespace Tests\Unit\Client;

use App\Models\Client;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class StoreTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function test_store(){
        $token = User::factory()->create()
            ->createToken('default')->plainTextToken;
        $data = [
            'name'              => $this->faker->name,
            'identification'    => $this->faker->bothify('??####'),
            'email'             => $this->faker->email,
        ];
        $this->post(route('client.store'), $data, [
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer '.$token
        ])->assertOk()
            ->assertJson([
                'status' => 'success',
                'client' => ['email' => $data['email']]
            ]);
        $this->assertDatabaseHas('clients', $data);
    }

    public function test_invalid_data(){
        $token = User::factory()->create()
            ->createToken('default')->plainTextToken;
        $this->post(route('client.store'), [], [
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer '.$token
        ])->assertStatus(400)
            ->assertJson(['status' => 'error'])
            ->assertJsonStructure(['errors']);
    }

    public function test_identification_or_ruc_or_email_exists(){
        $client = Client::factory()->create();
        $token = User::factory()->create()
            ->createToken('default')->plainTextToken;
        $data = [
            'identification'    => ['identification' => $client->identification],
            'ruc'               => ['ruc' => $client->ruc],
            'email'             => ['email' => $client->email]
        ];
        foreach ($data as $key => $value) {
            $this->post(route('client.store'), $value, [
                'Accept'        => 'application/json',
                'Authorization' => 'Bearer '.$token
            ])->assertStatus(400)
                ->assertJson(['status' => 'error'])
                ->assertJsonStructure(['errors' => [$key]]);
        }
    }
}
