<?php

namespace Tests\Unit\Provider;

use App\Models\Provider;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DestroyTest extends TestCase
{
    use RefreshDatabase;

    public function test_destroy(){
        $provider = Provider::factory()->create();
        $user = User::factory()->create();
        $token = $user->createToken('default')->plainTextToken;
        $this->delete(route('provider.destroy', ['provider' => $provider->id]), [], [
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer '.$token
        ])->assertOk()
            ->assertJson(['status' => 'success']);
        $this->assertDatabaseMissing('providers', ['id' => $provider->id]);
    }

    public function test_provider_does_not_exists(){
        $user = User::factory()->create();
        $token = $user->createToken('default')->plainTextToken;
        $this->delete(route('provider.destroy', ['provider' => random_int(10, 20)]), [], [
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer '.$token
        ])->assertStatus(400)
            ->assertJson(['status' => 'error']);
    }
}
