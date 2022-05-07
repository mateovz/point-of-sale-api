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
}
