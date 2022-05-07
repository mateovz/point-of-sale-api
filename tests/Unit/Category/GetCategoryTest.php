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
}
