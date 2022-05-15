<?php

namespace Tests\Unit\Purchase;

use App\Models\Purchase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetPurchaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_get(){
        Purchase::factory(5)->create();
        $token = (User::factory()->create())
            ->createToken('default')->plainTextToken;
        $this->get(route('purchase.index'), [
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer '.$token
        ])->assertOk()
            ->assertJson(['status' => 'success'])
            ->assertJsonCount(5, 'purchases');
    }

    public function test_get_with_user_and_provider_info(){
        Purchase::factory(5)->create();
        $token = (User::factory()->create())
            ->createToken('default', ['purchase.view', 'user.view', 'provider.view'])->plainTextToken;
        $this->get(route('purchase.index'), [
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer '.$token
        ])->assertOk()
            ->assertJson(['status' => 'success'])
            ->assertJsonCount(5, 'purchases')
            ->assertJsonStructure([
                'purchases' => [
                    '*' => [
                        'user' => ['email'],
                        'provider' => ['email']
                    ]
                ]
            ]);
    }

    public function test_get_purchase(){
        $token = (User::factory()->create())
            ->createToken('default')->plainTextToken;
        $purchase = Purchase::factory()->create();
        $this->get(route('purchase.show', ['purchase' => $purchase->id]), [
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer '.$token
        ])
            ->assertOk()
            ->assertJson([
                'status'    => 'success',
                'purchase'  => ['id' => $purchase->id]
            ])
            ->assertJsonStructure(['purchase' => ['user', 'provider']]);
    }

    public function test_get_null_purchase(){
        $token = (User::factory()->create())
            ->createToken('default')->plainTextToken;
        $this->get(route('purchase.show', ['purchase' => random_int(10, 20)]), [
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer '.$token
        ])
            ->assertStatus(400)
            ->assertJson(['status' => 'error'])
            ->assertJsonStructure(['errors' => ['purchase']]);
    }
}
