<?php

namespace Tests\Feature\Http\Controllers\Api\Order\OrderCommentController;

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
        $attributes = [
            'order_id' => $order->getKey(),
            'comment'  => 'Test comment',
        ];

        $response = $this->actingAs($user, 'api')->postJson(route('comments.store'), $attributes);

        $response->assertOk();

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'order_id',
                    'user_id',
                    'comment',
                ],
            ],
        ]);
    }
}
