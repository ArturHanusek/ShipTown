<?php

namespace Tests\Feature\Modules\InventoryMovements;

use App\Events\EveryDayEvent;
use App\Events\EveryFiveMinutesEvent;
use App\Events\EveryHourEvent;
use App\Events\EveryMinuteEvent;
use App\Events\EveryTenMinutesEvent;
use App\Events\SyncRequestedEvent;
use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\Warehouse;
use App\Modules\InventoryMovements\src\InventoryMovementsServiceProvider;
use App\Modules\InventoryMovements\src\Jobs\InventoryLastMovementIdJob;
use App\Modules\InventoryMovements\src\Jobs\InventoryQuantityJob;
use App\Modules\InventoryMovements\src\Jobs\PreviousMovementIdJob;
use App\Modules\InventoryMovements\src\Jobs\QuantityAfterJob;
use App\Modules\InventoryMovements\src\Jobs\QuantityBeforeJob;
use App\Modules\InventoryMovements\src\Jobs\QuantityDeltaJob;
use App\Modules\InventoryMovements\src\Models\Configuration;
use App\Services\InventoryService;
use Tests\TestCase;

class BasicModuleTest extends TestCase
{
    private InventoryMovement $inventoryMovement01;
    private InventoryMovement $inventoryMovement02;
    private Inventory $inventory;

    protected function setUp(): void
    {
        parent::setUp();

        InventoryMovementsServiceProvider::enableModule();

        /** @var Product $product */
        $product = Product::factory()->create();
        $warehouse = Warehouse::factory()->create();

        $this->inventory = Inventory::find($product->getKey(), $warehouse->getKey());

        $this->inventoryMovement01 = InventoryService::adjust($this->inventory, 20);
        $this->inventoryMovement02 = InventoryService::sell($this->inventory, -5);

        PreviousMovementIdJob::dispatch();
        InventoryLastMovementIdJob::dispatch();
        InventoryQuantityJob::dispatch();
    }

    /** @test */
    public function testInventoryQuantityJob()
    {
        $this->inventory->update([
            'quantity' => $this->inventory->quantity + rand(1, 100),
        ]);

        InventoryQuantityJob::dispatch();

        $this->assertDatabaseHas('inventory', [
            'id' => $this->inventory->getKey(),
            'last_movement_id' => $this->inventoryMovement02->getKey(),
            'quantity' => $this->inventoryMovement02->quantity_after,
        ]);
    }

    /** @test */
    public function testEmptyDatabaseRun()
    {
        PreviousMovementIdJob::dispatch();
        QuantityBeforeJob::dispatch();
        QuantityDeltaJob::dispatch();
        QuantityAfterJob::dispatch();
        InventoryLastMovementIdJob::dispatch();
        InventoryQuantityJob::dispatch();

        $this->assertTrue(true, 'We did not run into any errors');
    }

    /** @test */
    public function testIfNoErrorsDuringEvents()
    {
        SyncRequestedEvent::dispatch();
        EveryMinuteEvent::dispatch();
        EveryFiveMinutesEvent::dispatch();
        EveryTenMinutesEvent::dispatch();
        EveryHourEvent::dispatch();
        EveryDayEvent::dispatch();

        $this->assertTrue(true, 'Errors encountered while dispatching events');
    }

    /** @test */
    public function testPreviousMovementIdJob(): void
    {
        $this->inventoryMovement02->update([
            'quantity_before' => 100,
        ]);

        PreviousMovementIdJob::dispatch();

        $this->assertDatabaseHas('inventory_movements', [
            'id' => $this->inventoryMovement02->getKey(),
            'previous_movement_id' => $this->inventoryMovement01->getKey(),
        ]);
    }

    /** @test */
    public function testQuantityBeforeJob(): void
    {
        $movement = InventoryService::adjust($this->inventory, 10);

        $originalQuantityBefore = $movement->quantity_before;

        $movement->update([
            'quantity_before' => $originalQuantityBefore + rand(1, 100),
        ]);

        PreviousMovementIdJob::dispatch();
        QuantityBeforeJob::dispatch();
        InventoryQuantityJob::dispatch();

        ray(InventoryMovement::query()->get()->toArray(), Configuration::first()->toArray());

        $this->assertDatabaseHas('inventory_movements', [
            'id' => $movement->getKey(),
            'previous_movement_id' => $this->inventoryMovement02->getKey(),
            'quantity_before' => $originalQuantityBefore,
        ]);
    }

    /** @test */
    public function testQuantityDeltaAndAfterJob(): void
    {
        $inventoryMovement03 = InventoryService::adjust($this->inventory, 10);
        QuantityDeltaJob::dispatch();
        QuantityAfterJob::dispatch();

        $this->assertDatabaseHas('inventory_movements', [
            'id' => $this->inventoryMovement02->getKey(),
            'previous_movement_id' => $this->inventoryMovement01->getKey(),
            'quantity_before' => 20,
            'quantity_delta' => -5,
            'quantity_after' => 15,
        ]);
    }

    /** @test */
    public function testLastMovementIdJob(): void
    {
        Inventory::query()->update([
            'last_movement_id' => null,
            'last_movement_at' => null,
            'quantity' => 0,
        ]);

        InventoryLastMovementIdJob::dispatch();

        $this->assertDatabaseHas('inventory', [
            'id' => $this->inventory->id,
            'last_movement_id' => $this->inventoryMovement02->id,
            'last_movement_at' => $this->inventoryMovement02->occurred_at,
            'quantity' => $this->inventoryMovement02->quantity_after,
        ]);
    }
}
