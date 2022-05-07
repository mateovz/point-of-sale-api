<?php

namespace Tests\Unit\User;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function test_login(){
        $user = User::factory()->create();
        $data = ['email' => $user->email, 'password' => 'password'];
        $this->post(route('user.login'), $data)
            ->assertOk()
            ->assertJson([
                'status'    => 'success',
                'user'      => ['email' => $user->email]
            ]);
    }

    public function test_invalid_data_email(){
        $data = ['email' => $this->faker->email, 'password' => 'password'];
        $this->post(route('user.login'), $data)
            ->assertStatus(400)
            ->assertJson(['status' => 'error'])
            ->assertJsonCount(1, 'errors.email');
    }

    public function test_invalid_data_password(){
        $user = User::factory()->create();
        $data = ['email' => $user->email, 'password' => $this->faker->word];
        $this->post(route('user.login'), $data)
            ->assertStatus(401)
            ->assertJson(['status' => 'error'])
            ->assertJsonCount(1, 'errors');
    }
}
