<?php

namespace Tests\Unit\Sale;

use App\Models\Sale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetSaleTest extends TestCase
{
    use RefreshDatabase;

    public function test_get(){
        Sale::factory(5)->create();
        $token = User::factory()->create()
            ->createToken('default')->plainTextToken;
        $this->get(route('sale.index'), [
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer '.$token
        ])->assertOk()
            ->assertJson(['status' => 'success'])
            ->assertJsonCount(5, 'sales')
            ->assertJsonStructure([
                'sales' => [
                    '*' => [
                        'user',
                        'client',
                        'sale_details'
                    ]
                ]
            ]);
    }

    public function test_get_with_user_and_client_info(){
        Sale::factory(5)->create();
        $token = User::factory()->create()
            ->createToken('default', ['user.view', 'client.view'])->plainTextToken;
        $this->get(route('sale.index'), [
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer '.$token
        ])->assertOk()
            ->assertJson(['status' => 'success'])
            ->assertJsonCount(5, 'sales')
            ->assertJsonStructure([
                'sales' => [
                    '*' => [
                        'user'      => ['email'],
                        'client'    => ['email']
                    ]
                ]
            ]);
    }

    public function test_get_sale(){
        $token = User::factory()->create()
            ->createToken('default')->plainTextToken;
        $sale = Sale::factory()->create();
        $this->get(route('sale.show', ['sale' => $sale->id]), [
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer '.$token
        ])
            ->assertOk()
            ->assertJson([
                'status'    => 'success',
                'sale'  => ['id' => $sale->id]
            ])
            ->assertJsonStructure(['sale' => ['user', 'client']]);
    }

    public function test_get_null_sale(){
        $token = (User::factory()->create())
            ->createToken('default')->plainTextToken;
        $this->get(route('sale.show', ['sale' => random_int(10, 20)]), [
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer '.$token
        ])
            ->assertStatus(400)
            ->assertJson(['status' => 'error'])
            ->assertJsonStructure(['errors' => ['sale']]);
    }
}
