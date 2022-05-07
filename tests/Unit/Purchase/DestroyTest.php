<?php

namespace Tests\Unit\Purchase;

use App\Models\Purchase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DestroyTest extends TestCase
{
    use RefreshDatabase;

    public function test_destroy(){
        $purchase = Purchase::factory()->create();
        $token = User::factory()->create()
            ->createToken('default')->plainTextToken;
        $this->delete(route('purchase.destroy', ['purchase' => $purchase->id]), [], [
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer '.$token
        ])->assertOk()
            ->assertJson(['status' => 'success']);
        $this->assertDatabaseMissing('purchases', ['id' => $purchase->id]);
    }

    public function test_purchase_does_not_exists(){
        $user = User::factory()->create();
        $token = $user->createToken('default')->plainTextToken;
        $this->delete(route('purchase.destroy', ['purchase' => random_int(10, 20)]), [], [
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer '.$token
        ])->assertStatus(400)
            ->assertJson(['status' => 'error']);
    }
}
