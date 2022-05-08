<?php

namespace Tests\Unit\Category;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetCategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_get(){
        Category::factory(5)->create();
        $this->get(route('category.index'))
            ->assertOk()
            ->assertJson(['status' => 'success'])
            ->assertJsonCount(5, 'categories');
    }

    public function test_get_category(){
        $category = Category::factory()->create();
        $this->get(route('category.show', ['category' => $category->id]))
            ->assertOk()
            ->assertJson([
                'status'    => 'success',
                'category'  => ['id' => $category->id]
            ]);
    }

    public function test_get_null_category(){
        $this->get(route('category.show', ['category' => random_int(10, 20)]))
            ->assertStatus(400)
            ->assertJson(['status' => 'error'])
            ->assertJsonStructure(['errors' => ['category']]);
    }
}
