<?php

namespace Tests\Unit\Role;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function test_update(){
        $role = Role::factory()->create();
        $user = User::factory()->create();
        $token = $user->createToken('default')->plainTextToken;
        $data = [
            'name'  => $role->name,
            'slug'  => $this->faker->word,
            'description'   => $this->faker->paragraph
        ];
        $this->put(route('role.update', ['role' => $role->id]), $data, [
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer '.$token
        ])->assertOk()
            ->assertJson([
                'status'    => 'success',
                'role'      => ['id' => $role->id]
            ]);
        $this->assertDatabaseHas('roles', $data);
    }

    public function test_invalid_data(){
        $role = Role::factory()->create();
        $user = User::factory()->create();
        $token = $user->createToken('default')->plainTextToken;
        $this->put(route('role.update', ['role' => $role->id]), [], [
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer '.$token
        ])->assertStatus(400)
            ->assertJson(['status' => 'error'])
            ->assertJsonCount(2, 'errors');
    }

    public function test_name_or_slug_exists(){
        $role1 = Role::factory()->create();
        $role2 = Role::factory()->create();
        $user = User::factory()->create();
        $token = $user->createToken('default')->plainTextToken;
        $data = [
            'name'  => [
                'name'  => $role1->name,
                'slug'  => $this->faker->slug(2)
            ],
            'slug'  => [
                'name'  => $this->faker->word,
                'slug'  => $role1->slug
            ]
        ];
        foreach ($data as $key => $value) {
            $this->put(route('role.update', ['role' => $role2->id]), $value, [
                'Accept'        => 'application/json',
                'Authorization' => 'Bearer '.$token
            ])->assertStatus(400)
                ->assertJson(['status'  => 'error'])
                ->assertJsonCount(1, 'errors.'.$key);
        }
    }

    public function test_role_does_not_exists(){
        $user = User::factory()->create();
        $token = $user->createToken('default')->plainTextToken;
        $data = [
            'name'  => $this->faker->word,
            'slug'  => $this->faker->slug(2),
            'description'   => $this->faker->paragraph
        ];
        $this->put(route('role.update', ['role' => random_int(10, 20)]), $data, [
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer '.$token
        ])->assertStatus(400)
            ->assertJson(['status' => 'error']);
    }
}
