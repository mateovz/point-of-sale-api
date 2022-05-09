<?php

namespace Tests\Unit\Sale;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function test_update(){
        $sale = Sale::factory()
            ->has(SaleDetail::factory(3))
            ->create();
        $token = User::factory()->create()
            ->createToken('default')->plainTextToken;
        $data = [
            'tax' => random_int(1, 20),
        ];
        $extraData['products'][] = [
            'product_id'    => $sale->saleDetails[0]->id,
            'quantity'      => random_int(1, 5)
        ];
        $this->put(route('sale.update', ['sale' => $sale->id]), 
            array_merge($data, $extraData), [
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer '.$token
        ])->assertOk()
            ->assertJson(['status'    => 'success'])
            ->assertJsonStructure([
                'sale' => [
                    'user',
                    'client',
                    'sale_details' => [
                        '*' => ['product_id']
                    ]
                ]
            ]);
        $this->assertDatabaseHas('sales', $data);
        $this->assertDatabaseMissing('sales', array_merge($data, ['sale_data' => null]));
        $this->assertDatabaseHas('sale_details', array_merge(
            ['sale_id' => $sale->id],
            $extraData['products'][0]
        ));
    }

    public function test_add_product_to_sale(){
        $sale = Sale::factory()
            ->has(SaleDetail::factory(3))
            ->create();
        $token = User::factory()->create()
            ->createToken('default')->plainTextToken;
        $newProduct = Product::factory()->create();
        $data['products'][] = [
            'product_id'    => $newProduct->id,
            'quantity'      => random_int(1, 5)
        ];
        $this->put(route('sale.update', ['sale' => $sale->id]),
            $data ,[
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer '.$token
        ])->assertOk()
            ->assertJson(['status'    => 'success']);
        $this->assertDatabaseHas('sale_details', array_merge(
            ['sale_id' => $sale->id],
            $data['products'][0]
        ));
    }

    public function test_invalid_data(){
        $sale = Sale::factory()->create();
        $token = User::factory()->create()
            ->createToken('default')->plainTextToken;
        $data = ['client_id' => random_int(10, 20)];
        $this->put(route('sale.update', ['sale' => $sale->id]), $data, [
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer '.$token
        ])->assertStatus(400)
            ->assertJson(['status' => 'error'])
            ->assertJsonStructure(['errors']);
    }

    public function test_sale_does_not_exists(){
        $token = User::factory()->create()
            ->createToken('default')->plainTextToken;
        $data = [
            'tax' => random_int(1, 20)
        ];
        $this->put(route('sale.update', ['sale' => random_int(10, 20)]), $data, [
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer '.$token
        ])->assertStatus(400)
            ->assertJson(['status' => 'error'])
            ->assertJsonStructure(['errors' => ['sale']]);
    }
}
