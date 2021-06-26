<?php

namespace Tests\Feature\Http\Controllers\Api\Order\OrderShipmentController;

use App\Models\Order;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_store_call_returns_ok()
    {
        $user = factory(User::class)->create();
        $order = factory(Order::class)->create();

        $response = $this->actingAs($user, 'api')->postJson(route('shipments.store'), [
            'order_id'        => $order['id'],
            'shipping_number' => '123',
        ]);

        $response->assertStatus(201);

        $response->assertJsonStructure([
            'data' => [],
        ]);
    }
}
