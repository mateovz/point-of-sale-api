<?php

namespace Tests\Unit\Product;

use App\Models\Product;
use App\Models\Provider;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class getProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_get(){
        Product::factory(5)->create();
        $token = (User::factory()->create())
            ->createToken('default')->plainTextToken;
        $this->get(route('product.index'), [
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer '.$token
        ])->assertOk()
            ->assertJson(['status' => 'success'])
            ->assertJsonCount(5, 'products');
    }

    public function test_get_with_provider_info(){
        Product::factory(5)->create();
        $token = (User::factory()->create())
            ->createToken('default', ['provider.view'])->plainTextToken;
        $this->get(route('product.index'), [
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer '.$token
        ])->assertOk()
            ->assertJson(['status' => 'success'])
            ->assertJsonCount(5, 'products')
            ->assertJsonStructure([
                'products' => [
                    '*' => ['provider' => ['id', 'name', 'email']]
                ]
            ]);
    }

    public function test_get_no_provider_info(){
        Product::factory(5)->create();
        $provider = Provider::first();
        $token = (User::factory()->create())
            ->createToken('default', [])->plainTextToken;
        $this->get(route('product.index'), [
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer '.$token
        ])->assertOk()
            ->assertJson(['status' => 'success'])
            ->assertJsonCount(5, 'products')
            ->assertJsonStructure([
                'products'  => [
                    '*' => ['provider' => ['name']]
                ]
            ])
            ->assertJsonMissing([
                'products' => [
                    0 => [
                        'provider' => [
                            'email' => $provider->email
                        ]
                    ]
                ]
            ]);
    }

    public function test_get_product(){
        $token = (User::factory()->create())
            ->createToken('default')->plainTextToken;
        $product = Product::factory()->create();
        $this->get(route('product.show', ['product' => $product->id]), [
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer '.$token
        ])
            ->assertOk()
            ->assertJson([
                'status'    => 'success',
                'product'  => ['id' => $product->id]
            ])
            ->assertJsonStructure(['product' => ['provider']]);
    }

    public function test_get_null_product(){
        $token = (User::factory()->create())
            ->createToken('default')->plainTextToken;
        $this->get(route('product.show', ['product' => random_int(10, 20)]), [
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer '.$token
        ])
            ->assertStatus(400)
            ->assertJson(['status' => 'error'])
            ->assertJsonStructure(['errors' => ['product']]);
    }
}
