<?php

namespace Tests\Unit\Purchase;

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
            'provider_id'   => Provider::factory()->create()->id,
            'user_id'       => $user->id,
            'total'         => $this->faker->randomFloat(2, 0.01)
        ];
        $this->post(route('purchase.store'), $data, [
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer '.$token
        ])->assertOk()
            ->assertJson(['status' => 'success'])
            ->assertJsonStructure(['purchase']);
        $this->assertDatabaseHas('purchases', $data);
    }

    public function test_invalid_data(){
        $token = User::factory()->create()
            ->createToken('default')->plainTextToken;
        $this->post(route('purchase.store'), [], [
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer '.$token
        ])->assertStatus(400)
            ->assertJson(['status' => 'error'])
            ->assertJsonStructure(['errors']);
    }
}
