<?php

namespace Tests\External\Webhooks;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\Warehouse;
use App\Modules\Webhooks\src\Jobs\PublishInventoryMovementWebhooksJob;
use App\Modules\Webhooks\src\WebhooksServiceProviderBase;
use App\Services\InventoryService;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class InventoryMovementWebhookTest extends TestCase
{
    /** @test */
    public function test_module_basic_functionality()
    {
        WebhooksServiceProviderBase::enableModule();

        Bus::fake();

        factory(Product::class)->create();
        factory(Warehouse::class)->create();

        $inventory = Inventory::where([
            'product_id' => Product::first()->getKey(),
            'warehouse_id' => Warehouse::first()->getKey(),
        ])->first();

        InventoryService::adjustQuantity($inventory, 1, 'test');

        Bus::assertDispatchedAfterResponse(PublishInventoryMovementWebhooksJob::class);
    }
}