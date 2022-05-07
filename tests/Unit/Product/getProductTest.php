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
        $provider = Provider::first();
        $token = (User::factory()->create())
            ->createToken('default', ['provider.view'])->plainTextToken;
        $this->get(route('product.index'), [
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer '.$token
        ])->assertOk()
            ->assertJson(['status' => 'success'])
            ->assertJsonCount(5, 'products')
            ->assertJson([
                'products' => [
                   0 => [
                       'provider' => [
                            'id'    => $provider->id,
                            'name'  => $provider->name,
                            'email' => $provider->email
                       ]
                   ]
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
            ->assertJson([
                'products'  => [
                    0 => [
                        'provider' => [
                            'name'  => $provider->name
                        ]
                    ]
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
}
