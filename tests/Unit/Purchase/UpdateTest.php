<?php

namespace Tests\Unit\Purchase;

use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function test_update(){
        $purchase = Purchase::factory()
            ->has(PurchaseDetail::factory(3))
            ->create();
        $token = User::factory()->create()
            ->createToken('default')->plainTextToken;
        $data = [
            'tax' => random_int(1, 20),
        ];
        $extraData['products'][] = [
            'product_id'    => $purchase->purchaseDetails[0]->id,
            'quantity'      => random_int(1, 5)
        ];
        $this->put(route('purchase.update', ['purchase' => $purchase->id]),
            array_merge($data, $extraData) ,[
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer '.$token
        ])->assertOk()
            ->assertJson(['status'    => 'success'])
            ->assertJsonStructure([
                'purchase' => [
                    'user',
                    'provider',
                    'purchase_details' => [
                        '*' => ['product_id']
                    ]
                ]
            ]);
        $this->assertDatabaseHas('purchases', $data);
        $this->assertDatabaseMissing('purchases', array_merge($data, ['purchase_data' => null]));
        $this->assertDatabaseHas('purchase_details', array_merge(
            ['purchase_id' => $purchase->id],
            $extraData['products'][0]
        ));
    }

    public function test_invalid_data(){
        $purchase = Purchase::factory()->create();
        $token = User::factory()->create()
            ->createToken('default')->plainTextToken;
        $data = ['provider_id' => random_int(10, 20)];
        $this->put(route('purchase.update', ['purchase' => $purchase->id]), $data, [
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer '.$token
        ])->assertStatus(400)
            ->assertJson(['status' => 'error'])
            ->assertJsonStructure(['errors']);
    }

    public function test_purchase_does_not_exists(){
        $token = User::factory()->create()
            ->createToken('default')->plainTextToken;
        $data = [
            'tax' => random_int(1, 20)
        ];
        $this->put(route('purchase.update', ['purchase' => random_int(10, 20)]), $data, [
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer '.$token
        ])->assertStatus(400)
            ->assertJson(['status' => 'error'])
            ->assertJsonStructure(['errors' => ['purchase']]);
    }
}
