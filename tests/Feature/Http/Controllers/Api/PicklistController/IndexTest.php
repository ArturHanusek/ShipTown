<?php

namespace Tests\Feature\Http\Controllers\Api\PicklistController;

use App\Models\OrderProduct;
use App\Models\Warehouse;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IndexTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_index_call_returns_ok()
    {
        $warehouse = factory(Warehouse::class)->create();
//        OrderProduct::query()->forceDelete();
        $orderProduct = factory(OrderProduct::class)->create();

        $user = factory(User::class)->create();

        $response = $this->actingAs($user, 'api')->getJson(route('picklist.index'));

        $response->assertOk();

        $this->assertNotEquals(0, $response->json('meta.total'));

        $response->assertJsonStructure([
            'meta',
            'links',
            'data' => [
                '*' => [
                    'product_id',
                    'name_ordered',
                    'sku_ordered',
                    'total_quantity_to_pick',
                    'inventory_source_shelf_location',
                    'inventory_source_quantity',
                    'quantity_required',
                    'order_product_ids' => [],
                ],
            ],
        ]);
    }
}
