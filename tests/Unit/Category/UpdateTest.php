<?php

namespace Tests\Unit\Category;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function test_update(){
        $category = Category::factory()->create();
        $user = User::factory()->create();
        $token = $user->createToken('default')->plainTextToken;
        $data = [
            'name'  => $category->name,
            'description'  => $this->faker->paragraph
        ];
        $this->put(route('category.update', ['category' => $category->id]), $data, [
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer '.$token
        ])->assertOk()
            ->assertJson([
                'status'    => 'success',
                'category'      => ['id' => $category->id]
            ]);
        $this->assertDatabaseHas('categories', $data);
    }

    public function test_invalid_data(){
        $category = Category::factory()->create();
        $token = User::factory()->create()
            ->createToken('default')->plainTextToken;
        $data = ['name' => null];
        $this->put(route('category.update', ['category' => $category->id]), $data, [
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer '.$token
        ])->assertStatus(400)
            ->assertJson(['status' => 'error'])
            ->assertJsonStructure(['errors' => ['name']]);
    }

    public function test_name_exists(){
        $category1 = Category::factory()->create();
        $category2 = Category::factory()->create();
        $user = User::factory()->create();
        $token = $user->createToken('default')->plainTextToken;
        $data = [
            'name'  => $category1->name,
            'description'  => $this->faker->paragraph
        ];
        $this->put(route('category.update', ['category' => $category2->id]), $data, [
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer '.$token
        ])->assertStatus(400)
            ->assertJson(['status'  => 'error'])
            ->assertJsonCount(1, 'errors.name');
    }

    public function test_category_does_not_exists(){
        $user = User::factory()->create();
        $token = $user->createToken('default')->plainTextToken;
        $data = [
            'name'  => $this->faker->word,
            'slug'  => $this->faker->slug(2),
        ];
        $this->put(route('category.update', ['category' => random_int(10, 20)]), $data, [
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer '.$token
        ])->assertStatus(400)
            ->assertJson(['status' => 'error']);
    }
}
