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
}
