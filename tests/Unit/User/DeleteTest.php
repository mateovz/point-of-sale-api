<?php

namespace Tests\Unit\User;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_delete(){
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $token = $user2->createToken('default')->plainTextToken;
        $this->delete(route('user.delete', ['user' => $user1->id]), [], [
            'Authorization' => 'Bearer '.$token,
            'Accept'        => 'application/json'
        ])->assertOk()
            ->assertJson(['status' => 'success']);
        $this->assertDatabaseMissing('users', [
            'id'    => $user1->id
        ]);
    }

    public function test_user_null(){
        $user = User::factory()->create();
        $token = $user->createToken('default')->plainTextToken;
        $this->delete(route('user.delete', ['user' => random_int(10, 20)]), [], [
            'Authorization' => 'Bearer '.$token,
            'Accept'        => 'application/json'
        ])->assertStatus(400)
            ->assertJson(['status' => 'error']);
    }
}
