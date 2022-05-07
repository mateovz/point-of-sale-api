<?php

namespace Tests\Unit\User;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_logout(){
        $user = User::factory()->create();
        $token = $user->createToken('default')->plainTextToken;
        $this->post(route('user.logout'), [], [
            'Authorization' => 'Bearer '.$token,
            'Accept'        => 'application/json'
        ])->assertOk()
            ->assertJson(['status' => 'success']);
        $this->assertNull($user->tokens()->where('name', 'default')->first());
    }
}
