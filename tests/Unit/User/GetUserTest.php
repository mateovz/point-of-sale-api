<?php

namespace Tests\Unit\User;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetUserTest extends TestCase
{
    use RefreshDatabase;

    public function test_get(){
        User::factory(5)->create();
        $token = User::factory()->create()
            ->createToken('default')->plainTextToken;
        $this->get(route('user.index'), [
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer '.$token
        ])->assertOk()
            ->assertJson(['status' => 'success'])
            ->assertJsonCount(5, 'users')
            ->assertJsonStructure([
                'users' => [
                    '*' => [
                        'roles' => [
                            '*' => [
                                'permissions'
                            ]
                        ]
                    ]
                ]
            ]);
    }

    public function test_get_user(){
        $user = User::factory()->create();
        $token = User::factory()->create()
            ->createToken('default')->plainTextToken;
        $this->get(route('user.show', ['user' => $user->id]), [
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer '.$token
        ])->assertOk()
            ->assertJson(['status' => 'success'])
            ->assertJsonStructure([
                'user' => [
                    'roles' => [
                        '*' => [
                            'permissions'
                        ]
                    ]
                ]
            ]);
    }
}
