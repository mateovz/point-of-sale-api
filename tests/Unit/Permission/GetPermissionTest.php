<?php

namespace Tests\Unit\Permission;

use App\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetPermissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_get(){
        Permission::factory(5)->create();
        $this->get(route('permission.index'))
            ->assertOk()
            ->assertJson(['status' => 'success'])
            ->assertJsonCount(5, 'permissions');
    }
}
