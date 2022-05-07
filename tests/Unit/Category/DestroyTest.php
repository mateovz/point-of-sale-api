<?php

namespace Tests\Unit\Category;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DestroyTest extends TestCase
{
    use RefreshDatabase;

    public function test_destroy(){
        $category = Category::factory()->create();
        $user = User::factory()->create();
        $token = $user->createToken('default')->plainTextToken;
        $this->delete(route('category.destroy', ['category' => $category->id]), [], [
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer '.$token
        ])->assertOk()
            ->assertJson(['status' => 'success']);
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_category_does_not_exists(){
        $user = User::factory()->create();
        $token = $user->createToken('default')->plainTextToken;
        $this->delete(route('category.destroy', ['category' => random_int(10, 20)]), [], [
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer '.$token
        ])->assertStatus(400)
            ->assertJson(['status' => 'error']);
    }
}
