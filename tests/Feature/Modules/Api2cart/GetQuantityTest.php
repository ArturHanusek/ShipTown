<?php

namespace Tests\Feature\Modules\Api2cart;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\Warehouse;
use App\Modules\Api2cart\src\Models\Api2cartConnection;
use App\Modules\Api2cart\src\Models\Api2cartProductLink;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;
use Tests\TestCase;

class GetQuantityTest extends TestCase
{
    use RefreshDatabase;

    private Api2cartConnection $api2cartConnection;
    private Api2cartProductLink $productLink;

    protected function setUp(): void
    {
        parent::setUp();

        factory(Warehouse::class, 10)->create();

        $product = factory(Product::class)->create();

        Inventory::query()->each(function (Inventory $inventory) {
            $inventory->update(['quantity' => $inventory->warehouse_id]);
        });

        $this->api2cartConnection = factory(Api2cartConnection::class)->create();

        $this->productLink = new Api2cartProductLink();
        $this->productLink->api2cart_connection_id = $this->api2cartConnection->getKey();
        $this->productLink->product_id = $product->getKey();
        $this->productLink->save();
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testEmptyInventoryWarehouseIds()
    {
        $this->api2cartConnection->update(['inventory_warehouse_ids' => []]);

        $this->assertEquals(55, $this->productLink->getProductData()['quantity']);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testSetInventoryWarehouseIds()
    {
        $warehouseIds = Warehouse::select('id')
            ->limit(5)
            ->inRandomOrder()
            ->get()
            ->map(function (Warehouse $warehouse) {
                return $warehouse->getKey();
            })
            ->toArray();

        $this->api2cartConnection->update(['inventory_warehouse_ids' => $warehouseIds]);

        $idsSum = collect($warehouseIds)->sum(function ($warehouse) {
            return $warehouse;
        });

        $this->assertEquals($idsSum, $this->productLink->getProductData()['quantity']);
    }
}
