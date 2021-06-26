<?php

namespace Tests\Feature\Http\Controllers\Api\Order\OrderProductController;

use App\Models\OrderProduct;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IndexTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_index_call_returns_ok()
    {
        $user = factory(User::class)->create();

        factory(OrderProduct::class)->create();

        $response = $this->actingAs($user, 'api')->getJson(route('order.products.index', [
            'include'=> [
                'order',
                'product',
                'product.aliases',
            ],
        ]));

        $response->assertOk();

        $this->assertNotEquals(0, $response->json('meta.total'));

        $response->assertJsonStructure([
            'meta',
            'links',
            'data' => [
                '*' => [
                    'id',
                    'order_id',
                    'product_id',
                    'sku_ordered',
                    'name_ordered',
                    'quantity_ordered',
                    'quantity_picked',
                    'quantity_shipped',
                    'order'   => [],
                    'product' => [
                        'aliases' => [],
                    ],
                ],
            ],
        ]);
    }
}
