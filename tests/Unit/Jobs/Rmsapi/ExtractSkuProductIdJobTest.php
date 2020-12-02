<?php

namespace Tests\Unit\Jobs\Rmsapi;

use App\Jobs\Modules\Rmsapi\ProcessImportedProductsJob;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\RmsapiProductImport;
use App\Modules\Rmsapi\src\Jobs\ExtractSkuAndProductIdJob;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class ExtractSkuProductIdJobTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testIfAllSkuArePopulated()
    {
        Event::fake();

        // prepare
        Product::query()->forceDelete();
        Inventory::query()->delete();

        RmsapiProductImport::query()->delete();

        factory(RmsapiProductImport::class, 5)->create([
            'sku' => null,
            'product_id' => null,
            'when_processed' => null,
        ]);

        ProcessImportedProductsJob::dispatchNow();

        RmsapiProductImport::query()->update([
            'sku' => null,
            'product_id' => null,
        ]);

        // do
        ExtractSkuAndProductIdJob::dispatchNow();


        // assert
        $this->assertFalse(
            RmsapiProductImport::query()
                ->whereNotNull('when_processed')
                ->whereNull('sku')
                ->exists(),
            'sku column is not populated'
        );

        $this->assertFalse(
            RmsapiProductImport::query()
                ->whereNotNull('when_processed')
                ->whereNull('product_id')
                ->exists(),
            'product_id column is not populated'
        );
    }
}
