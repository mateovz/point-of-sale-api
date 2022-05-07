<?php

namespace Tests\Unit\Role;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class StoreTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function test_store(){
        $user = User::factory()->create();
        $token = $user->createToken('default')->plainTextToken;
        $data = [
            'name'  => $this->faker->word,
            'slug'  => $this->faker->word,
            'description'   => $this->faker->paragraph
        ];
        $this->post(route('role.store'), $data, [
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer '.$token
        ])->assertOk()
            ->assertJson([
                'status'    => 'success',
                'role'      => ['name'  => $data['name']]
            ]);
        $this->assertDatabaseHas('roles', $data);
    }

    public function test_invalid_data(){
        $user = User::factory()->create();
        $token = $user->createToken('default')->plainTextToken;
        $this->post(route('role.store'), [], [
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer '.$token
        ])->assertStatus(400)
            ->assertJson(['status' => 'error'])
            ->assertJsonCount(2, 'errors');
    }

    public function test_name_or_slug_exists(){
        $role = Role::factory()->create();
        $user = User::factory()->create();
        $token = $user->createToken('default')->plainTextToken;
        $data = [
            'name' => [
                'name'  => $role->name,
                'slug'  => $this->faker->word
            ],
            'slug' => [
                'name'  => $this->faker->word,
                'slug'  => $role->slug
            ]
        ];
        foreach ($data as $key => $value) {
            $this->post(route('role.store'), $value, [
                'Accept'        => 'application/json',
                'Authorization' => 'Bearer '.$token
            ])->assertStatus(400)
                ->assertJson(['status' => 'error'])
                ->assertJsonCount(1, 'errors.'.$key);
        }
    }
}
