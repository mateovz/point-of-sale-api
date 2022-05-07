<?php

namespace Tests\Unit\Category;

use App\Models\Category;
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
            'name'  => $this->faker->word,
            'description' => $this->faker->paragraph
        ];
        $this->post(route('category.store'), $data, [
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer '.$token
        ])->assertOk()
            ->assertJson([
                'status' => 'success',
                'category' => ['name' => $data['name']]
            ]);
        $this->assertDatabaseHas('categories', $data);
    }

    public function test_invalid_data(){
        $user = User::factory()->create();
        $token = $user->createToken('default')->plainTextToken;
        $this->post(route('category.store'), [], [
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer '.$token
        ])->assertStatus(400)
            ->assertJson(['status' => 'error'])
            ->assertJsonCount(1, 'errors');
    }

    public function test_name_exists(){
        $category = Category::factory()->create();
        $user = User::factory()->create();
        $token = $user->createToken('default')->plainTextToken;
        $data = ['name'  => $category->name];
        $this->post(route('category.store'), $data, [
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer '.$token
        ])->assertStatus(400)
            ->assertJson(['status' => 'error'])
            ->assertJsonCount(1, 'errors.name');
    }
}
