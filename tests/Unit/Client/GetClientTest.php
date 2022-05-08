<?php

namespace Tests\Unit\Client;

use App\Models\Client;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetClientTest extends TestCase
{
    use RefreshDatabase;

    public function test_get(){
        Client::factory(5)->create();
        $token = (User::factory()->create())
            ->createToken('default')->plainTextToken;
        $this->get(route('client.index'), [
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer '.$token
        ])->assertOk()
            ->assertJson(['status' => 'success'])
            ->assertJsonCount(5, 'clients');
    }

    public function test_get_client(){
        $client = Client::factory()->create();
        $token = (User::factory()->create())
            ->createToken('default')->plainTextToken;
        $this->get(route('client.show', ['client' => $client->id]), [
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer '.$token
        ])
            ->assertOk()
            ->assertJson([
                'status'    => 'success',
                'client'  => ['id' => $client->id]
            ]);
    }

    public function test_get_null_client(){
        $token = (User::factory()->create())
            ->createToken('default')->plainTextToken;
        $this->get(route('client.show', ['client' => random_int(10, 20)]), [
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer '.$token
        ])
            ->assertStatus(400)
            ->assertJson(['status' => 'error'])
            ->assertJsonStructure(['errors' => ['client']]);
    }
}
