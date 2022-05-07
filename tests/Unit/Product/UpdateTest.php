<?php

namespace Tests\Unit\Product;

use App\Models\Category;
use App\Models\Product;
use App\Models\Provider;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function test_update(){
        $product = Product::factory()->create();
        $token = User::factory()->create()
            ->createToken('default')->plainTextToken;
        $data = [
            'category_id'   => $product->category_id,
            'provider_id'   => $product->provider_id,
            'name'          => $this->faker->words(2, true),
            'stock'         => random_int(1, 100),
            'price'         => $product->price,
            'status'        => 0,
            'code'          => $product->code
        ];
        $this->put(route('product.update', ['product' => $product->id]), $data, [
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer '.$token
        ])->assertOk()
            ->assertJson([
                'status'    => 'success',
                'product'  => ['id' => $product->id]
            ])
            ->assertJsonStructure([
                'status',
                'product' => [
                    'category',
                    'provider'
                ]
            ]);
        $this->assertDatabaseHas('products', $data);
    }

    public function test_invalid_data(){
        $product = Product::factory()->create();
        $token = User::factory()->create()
            ->createToken('default')->plainTextToken;
        $data = ['provider_id' => null];
        $this->put(route('product.update', ['product' => $product->id]), $data, [
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer '.$token
        ])->assertStatus(400)
            ->assertJson(['status' => 'error'])
            ->assertJsonStructure(['errors' => ['provider_id']]);
    }

    public function test_name__or_code_exists(){
        $product1 = Product::factory()->create();
        $product2 = Product::factory()->create();
        $token = User::factory()->create()
            ->createToken('default')->plainTextToken;
        $data = [
            'name' => ['name' => $product1->name],
            'code' => ['code' => $product1->code]
        ];
        foreach ($data as $key => $value) {
            $this->put(route('product.update', ['product' => $product2->id]), $value, [
                'Accept'        => 'application/json',
                'Authorization' => 'Bearer '.$token
            ])->assertStatus(400)
                ->assertJson(['status'  => 'error'])
                ->assertJsonCount(1, 'errors.'.$key);
        }
    }

    public function test_product_does_not_exists(){
        $token = User::factory()->create()
            ->createToken('default')->plainTextToken;
        $data = [
            'category_id'   => Category::factory()->create()->id,
            'provider_id'   => Provider::factory()->create()->id,
            'name'          => $this->faker->words(2, true),
            'stock'         => random_int(1, 100),
            'price'         => $this->faker->randomFloat(2, 0, 50),
            'status'        => true,
            'code'          => $this->faker->numerify('####-####-####')
        ];
        $this->put(route('product.update', ['product' => random_int(10, 20)]), $data, [
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer '.$token
        ])->assertStatus(400)
            ->assertJson(['status' => 'error'])
            ->assertJsonStructure([
                'status', 'errors'
            ]);
    }
}
