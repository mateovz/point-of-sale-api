<?php

namespace Tests\Unit\User;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateRoleUser extends TestCase
{
    use RefreshDatabase;

    public function test_add_role_user(){
        $user = User::factory()->create();
        $roles = Role::factory(3)->create();
        $token = User::factory()->create()
            ->createToken('default')->plainTextToken;
        $data = [
            'roles' => [
                'add' => [
                    ['id' => $roles[0]->id],
                    ['id' => $roles[1]->id],
                    ['id' => $roles[2]->id]
                ]
            ]
        ];
        $this->put(route('user.update', ['user' => $user->id]), $data, [
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer '.$token
        ])->assertOk()
            ->assertJson(['status' => 'success']);

        foreach ($roles as $role) {
            $this->assertDatabaseHas('role_user', [
                'role_id' => $role->id,
                'user_id' => $user->id
            ]);
        }
    }
}
