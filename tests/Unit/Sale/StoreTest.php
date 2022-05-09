<?php

namespace Tests\Unit\Sale;

use App\Models\Client;
use App\Models\Product;
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
        $products = Product::factory(5)->create();
        $data = [
            'client_id'     => Client::factory()->create()->id,
            'user_id'       => $user->id
        ];
        $extraData = $data;
        $total = 0;
        foreach ($products as $product) {
            $extraData['products'][$product->id] = [
                'product_id'    => $product->id,
                'quantity'      => random_int(1, 10),
                'discount'      => random_int(0, 5)
            ];
            $newTotal = ($product->price * $extraData['products'][$product->id]['quantity']);
            $discount = $newTotal * ($extraData['products'][$product->id]['discount'] / 100);
            $total += ($newTotal - $discount);
        }
        $this->post(route('sale.store'), $extraData, [
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer '.$token
        ])->assertOk()
            ->assertJson(['status' => 'success'])
            ->assertJsonStructure([
                'sale' => [
                    'user',
                    'client',
                    'sale_details'
                ]
            ])
            ->assertJsonCount(5, 'sale.sale_details')
            ->assertJson(['sale' => ['total' => $total]]);
        $this->assertDatabaseHas('sales', $data);
        foreach ($extraData['products'] as $product) {
            $this->assertDatabaseHas('sale_details', $product);
        }
    }

    public function test_invalid_data(){
        $token = User::factory()->create()
            ->createToken('default')->plainTextToken;
        $this->post(route('sale.store'), [], [
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer '.$token
        ])->assertStatus(400)
            ->assertJson(['status' => 'error'])
            ->assertJsonStructure(['errors']);
    }
}
