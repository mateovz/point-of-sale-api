<?php

namespace Tests\Unit\Permission;

use App\Models\Permission;
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
            'slug'  => $this->faker->word
        ];
        $this->post(route('permission.store'), $data, [
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer '.$token
        ])->assertOk()
            ->assertJson([
                'status'        => 'success',
                'permission'    => ['name'  => $data['name']]
            ]);
        $this->assertDatabaseHas('permissions', $data);
    }

    public function test_invalid_data(){
        $user = User::factory()->create();
        $token = $user->createToken('default')->plainTextToken;
        $this->post(route('permission.store'), [], [
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer '.$token
        ])->assertStatus(400)
            ->assertJson(['status' => 'error'])
            ->assertJsonCount(2, 'errors');
    }

    public function test_name_or_slug_exists(){
        $permission = Permission::factory()->create();
        $user = User::factory()->create();
        $token = $user->createToken('default')->plainTextToken;
        $data = [
            'name' => [
                'name'  => $permission->name,
                'slug'  => $this->faker->word
            ],
            'slug' => [
                'name'  => $this->faker->word,
                'slug'  => $permission->slug
            ]
        ];
        foreach ($data as $key => $value) {
            $this->post(route('permission.store'), $value, [
                'Accept'        => 'application/json',
                'Authorization' => 'Bearer '.$token
            ])->assertStatus(400)
                ->assertJson(['status' => 'error'])
                ->assertJsonCount(1, 'errors.'.$key);
        }
    }
}
