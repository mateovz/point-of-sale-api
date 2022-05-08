<?php

namespace Tests\Unit\Role;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetRolesTest extends TestCase
{
    use RefreshDatabase;

    public function test_get(){
        Role::factory(5)->create();
        $this->get(route('role.index'))
            ->assertOk()
            ->assertJson(['status' => 'success'])
            ->assertJsonCount(5, 'roles')
            ->assertJsonStructure([
                'roles' => [
                    '*' => ['permissions']
                ]
            ]);
    }

    public function test_get_role(){
        $role = Role::factory()->create();
        $this->get(route('role.show', ['role' => $role->id]))
            ->assertOk()
            ->assertJson([
                'status'    => 'success',
                'role'  => ['id' => $role->id]
            ])
            ->assertJsonStructure(['role' => ['permissions']]);
    }

    public function test_get_null_role(){
        $this->get(route('role.show', ['role' => random_int(10, 20)]))
            ->assertStatus(400)
            ->assertJson(['status' => 'error'])
            ->assertJsonStructure(['errors' => ['role']]);
    }
}
