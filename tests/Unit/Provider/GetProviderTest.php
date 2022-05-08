<?php

namespace Tests\Unit\Provider;

use App\Models\Provider;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetProviderTest extends TestCase
{
    use RefreshDatabase;

    public function test_get(){
        $token = (User::factory()->create())
            ->createToken('default')->plainTextToken;
        Provider::factory(5)->create();
        $this->get(route('provider.index'), [
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer '.$token
        ])->assertOk()
            ->assertJson(['status' => 'success'])
            ->assertJsonCount(5, 'providers');
    }

    public function test_auth_failed(){
        Provider::factory(5)->create();
        $this->get(route('provider.index'), [
            'Accept'        => 'application/json'
        ])->assertStatus(401)
            ->assertJson(['message' => 'Unauthenticated.']);
    }

    public function test_get_provider(){
        $token = (User::factory()->create())
            ->createToken('default')->plainTextToken;
        $provider = Provider::factory()->create();
        $this->get(route('provider.show', ['provider' => $provider->id]), [
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer '.$token
        ])
            ->assertOk()
            ->assertJson([
                'status'    => 'success',
                'provider'  => ['id' => $provider->id]
            ]);
    }

    public function test_get_null_provider(){
        $token = (User::factory()->create())
            ->createToken('default')->plainTextToken;
        $this->get(route('provider.show', ['provider' => random_int(10, 20)]), [
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer '.$token
        ])
            ->assertStatus(400)
            ->assertJson(['status' => 'error'])
            ->assertJsonStructure(['errors' => ['provider']]);
    }
}
