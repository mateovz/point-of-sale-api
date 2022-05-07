<?php

namespace Tests\Unit\Product;

use App\Models\Category;
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
        $data = [
            'category_id'   => Category::factory()->create()->id,
            'provider_id'   => Provider::factory()->create()->id,
            'name'          => $this->faker->words(2, true),
            'stock'         => random_int(0, 100),
            'price'         => $this->faker->randomFloat(2, 0, 50),
            'status'        => true,
            'code'          => $this->faker->numerify('####-####-####')
        ];
        $this->post(route('product.store'), $data, [
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer '.$token
        ])->assertOk()
            ->assertJson([
                'status' => 'success',
                'product' => ['name' => $data['name']]
            ]);
        $this->assertDatabaseHas('products', $data);
    }

    public function test_invalid_data(){
        $user = User::factory()->create();
        $token = $user->createToken('default')->plainTextToken;
        $this->post(route('product.store'), [], [
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer '.$token
        ])->assertStatus(400)
            ->assertJson(['status' => 'error'])
            ->assertJsonCount(7, 'errors');
    }

    public function test_name_exists(){
        $product = Product::factory()->create();
        $user = User::factory()->create();
        $token = $user->createToken('default')->plainTextToken;
        $data = ['name'  => $product->name];
        $this->post(route('product.store'), $data, [
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer '.$token
        ])->assertStatus(400)
            ->assertJson(['status' => 'error'])
            ->assertJsonCount(1, 'errors.name');
    }
}
