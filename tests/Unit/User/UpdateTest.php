<?php

namespace Tests\Unit\User;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function test_update(){
        $user = User::factory()->create();
        $token = User::factory()->create()
            ->createToken('default')->plainTextToken;
        $data = [
            'name'  => $this->faker->firstName,
            'email' => $user->email
        ];
        $this->put(route('user.update', ['user' => $user->id]), $data, [
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer '.$token
        ])->assertOk()
            ->assertJson([
                'status'    => 'success',
                'user'      => ['email' => $data['email']]
            ]);
        $this->assertDatabaseHas('users', [
            'id'    => $user->id,
            'name'  => $data['name'],
            'email' => $data['email']
        ]);
    }

    public function test_invalid_data(){
        $token = User::factory()->create()
            ->createToken('default')->plainTextToken;
        $data = ['email' => null];
        $this->put(route('user.update', ['user' => random_int(10, 20)]), $data, [
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer '.$token
        ])->assertStatus(400)
            ->assertJson(['status' => 'error'])
            ->assertJsonStructure(['errors' => ['email']]);
    }

    public function test_email_in_use(){
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $token = $user2->createToken('default')->plainTextToken;
        $data = [
            'email' => $user1->email,
            'name'  => $this->faker->firstName
        ];
        $this->put(route('user.update', ['user' => $user2->id]), $data, [
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer '.$token
        ])->assertStatus(400)
            ->assertJson(['status'  => 'error'])
            ->assertJsonCount(1, 'errors.email');
    }
}
