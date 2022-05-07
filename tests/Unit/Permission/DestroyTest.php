<?php

namespace Tests\Unit\Permission;

use App\Models\Permission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DestroyTest extends TestCase
{
    use RefreshDatabase;

    public function test_destroy(){
        $permission = Permission::factory()->create();
        $user = User::factory()->create();
        $token = $user->createToken('default')->plainTextToken;
        $this->delete(route('permission.destroy', ['permission' => $permission->id]), [], [
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer '.$token
        ])->assertOk()
            ->assertJson(['status' => 'success']);
        $this->assertDatabaseMissing('permissions', ['id' => $permission->id]);
    }

    public function test_permission_does_not_exists(){
        $user = User::factory()->create();
        $token = $user->createToken('default')->plainTextToken;
        $this->delete(route('permission.destroy', ['permission' => random_int(10, 20)]), [], [
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer '.$token
        ])->assertStatus(400)
            ->assertJson(['status' => 'error']);
    }
}
