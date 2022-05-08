<?php

namespace Tests\Unit\Purchase;

use App\Models\Product;
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
        $products = Product::factory(5)->create();
        $data = [
            'provider_id'   => Provider::factory()->create()->id,
            'user_id'       => $user->id
        ];
        $extraData = $data;
        $total = 0;
        foreach ($products as $product) {
            $extraData['products'][$product->id] = [
                'product_id'    => $product->id,
                'quantity'      => random_int(1, 10)
            ];
            $total += ($product->price * $extraData['products'][$product->id]['quantity']);
        }
        $result = $this->post(route('purchase.store'), $extraData, [
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer '.$token
        ])->assertOk()
            ->assertJson(['status' => 'success'])
            ->assertJsonStructure(['purchase' => ['purchase_details']])
            ->assertJsonCount(5, 'purchase.purchase_details')
            ->assertJson(['purchase' => ['total' => $total]]);
        $this->assertDatabaseHas('purchases', $data);
        foreach ($extraData['products'] as $product) {
            $this->assertDatabaseHas('purchase_details', $product);
        }
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
