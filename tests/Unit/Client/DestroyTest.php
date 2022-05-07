<?php

namespace Tests\Unit\Client;

use App\Models\Client;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DestroyTest extends TestCase
{
    use RefreshDatabase;

    public function test_destroy(){
        $client = Client::factory()->create();
        $token = User::factory()->create()
            ->createToken('default')->plainTextToken;
        $this->delete(route('client.destroy', ['client' => $client->id]), [], [
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer '.$token
        ])->assertOk()
            ->assertJson(['status' => 'success']);
        $this->assertDatabaseMissing('clients', ['id' => $client->id]);
    }

    public function test_client_does_not_exists(){
        $token = User::factory()->create()
            ->createToken('default')->plainTextToken;
        $this->delete(route('client.destroy', ['client' => random_int(10, 20)]), [], [
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer '.$token
        ])->assertStatus(400)
            ->assertJson(['status' => 'error']);
    }
}
