<?php

namespace Tests\Feature\Http\Controllers\Api\RestockingController;

use App\Models\Product;
use App\Models\Warehouse;
use App\User;
use Tests\TestCase;

class IndexTest extends TestCase
{
    /** @test */
    public function test_index_call_returns_ok()
    {
        factory(Warehouse::class)->create();
        factory(Product::class)->create();
        $user = factory(User::class)->create();

        $response = $this->actingAs($user, 'api')->getJson(route('restocking.index'));

        $response->assertOk();

        $this->assertEquals(4, $response->json('meta.total'), 'No records returned');

        ray($response->json());

        $response->assertJsonStructure([
            'meta',
            'links',
            'data' => [
                '*' => [
                    'warehouse_code',
                    'product_sku',
                    'product_name',
                    'quantity_required',
                    'quantity_available',
                    'quantity_incoming',
                    'reorder_point',
                    'restock_level',
                    'warehouse_quantity',
                ],
            ],
        ]);
    }
}
