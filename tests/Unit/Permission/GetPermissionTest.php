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

    public function test_get_permission(){
        $permission = Permission::factory()->create();
        $this->get(route('permission.show', ['permission' => $permission->id]))
            ->assertOk()
            ->assertJson([
                'status'    => 'success',
                'permission'  => ['id' => $permission->id]
            ])
            ->assertJsonStructure([
                'permission' => ['roles']
            ]);
    }

    public function test_get_null_permission(){
        $this->get(route('permission.show', ['permission' => random_int(10, 20)]))
            ->assertStatus(400)
            ->assertJson(['status' => 'error'])
            ->assertJsonStructure(['errors' => ['permission']]);
    }
}
