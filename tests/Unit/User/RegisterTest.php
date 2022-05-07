<?php

namespace Tests\Unit\User;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function test_register(){
        $user = User::factory()->create();
        $token = $user->createToken('default')->plainTextToken;
        $data = [
            'name'  => $this->faker->firstName,
            'email' => $this->faker->email,
            'password'  => $this->faker->password
        ];
        $this->post(route('user.register'), $data, [
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer '.$token
        ])->assertOk()
            ->assertJson(['status' => 'success']);
        $this->assertDatabaseHas('users', [
            'name'  => $data['name'],
            'email' => $data['email']
        ]);
    }

    public function test_invalid_data(){
        $user = User::factory()->create();
        $token = $user->createToken('default')->plainTextToken;
        $this->post(route('user.register'), [], [
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer '.$token
        ])->assertStatus(400)
            ->assertJson(['status' => 'error'])
            ->assertJsonCount(3, 'errors');
    }

    public function test_email_in_use(){
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $token = $user2->createToken('default')->plainTextToken;
        $data = [
            'name'  => $this->faker->firstName,
            'email' => $user1->email,
            'password'  => $this->faker->password
        ];
        $this->post(route('user.register'), $data, [
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer '.$token
        ])->assertStatus(400)
            ->assertJson(['status' => 'error'])
            ->assertJsonCount(1, 'errors.email');
    }
}
