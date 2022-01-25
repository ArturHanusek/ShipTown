<?php

namespace Tests\Unit\Jobs\Maintenance;

use App\Events\HourlyEvent;
use App\Models\AutoStatusPickingConfiguration;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use App\Modules\AutoStatusPicking\src\AutoStatusPickingServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RefillWebPickingStatusListJobTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExample()
    {
        AutoStatusPickingServiceProvider::enableModule();

        Product::query()->forceDelete();
        OrderProduct::query()->forceDelete();
        Order::query()->forceDelete();

        factory(Product::class, 30)->create();

        factory(Order::class, 150)
            ->with('orderProducts', 2)
            ->create(['status_code' => 'paid']);

        HourlyEvent::dispatch();

        $this->assertEquals(
            AutoStatusPickingConfiguration::firstOrCreate([], [])->max_batch_size,
            AutoStatusPickingConfiguration::firstOrCreate([], [])->current_count_with_status,
        );
    }
}
