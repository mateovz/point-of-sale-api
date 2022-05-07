<?php

namespace Tests\Unit\Role;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DestroyTest extends TestCase
{
    use RefreshDatabase;

    public function test_destroy(){
        $role = Role::factory()->create();
        $user = User::factory()->create();
        $token = $user->createToken('default')->plainTextToken;
        $this->delete(route('role.destroy', ['role' => $role->id]), [], [
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer '.$token
        ])->assertOk()
            ->assertJson(['status' => 'success']);
        $this->assertDatabaseMissing('roles', ['id' => $role->id]);
    }

    public function test_role_does_not_exists(){
        $user = User::factory()->create();
        $token = $user->createToken('default')->plainTextToken;
        $this->delete(route('role.destroy', ['role' => random_int(10, 20)]), [], [
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer '.$token
        ])->assertStatus(400)
            ->assertJson(['status' => 'error']);
    }
}
