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
            ->assertJsonCount(5, 'roles');
    }
}
