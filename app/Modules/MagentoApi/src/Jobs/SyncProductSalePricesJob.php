<?php

namespace App\Modules\MagentoApi\src\Jobs;

use App\Modules\MagentoApi\src\Models\MagentoProductPricesComparisonView;
use App\Modules\MagentoApi\src\Services\MagentoService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Class SyncCheckFailedProductsJob.
 */
class SyncProductSalePricesJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $collection = MagentoProductPricesComparisonView::query()
            ->whereNotNull('special_prices_fetched_at')
            ->whereRaw('special_prices_fetched_at IS NOT NULL
            AND (
                magento_sale_price != expected_sale_price
                OR magento_sale_price_start_date != expected_sale_price_start_date
                OR magento_sale_price_end_date != expected_sale_price_end_date
                OR magento_sale_price IS NULL
                OR magento_sale_price_start_date IS NULL
                OR magento_sale_price_end_date IS NULL
            )')
            ->limit(50)
            ->get();

        if ($collection->isEmpty()) {
            return;
        }

        $collection->each(function (MagentoProductPricesComparisonView $comparison) {
            MagentoService::updateSalePrice(
                $comparison->sku,
                $comparison->expected_sale_price,
                $comparison->expected_sale_price_start_date,
                $comparison->expected_sale_price_end_date,
                $comparison->magento_store_id
            );

            $comparison->magentoProduct->update([
                'special_prices_fetched_at'     => null,
                'special_prices_raw_import'     => null,
                'magento_sale_price'            => null,
                'magento_sale_price_start_date' => null,
                'magento_sale_price_end_date'   => null,
            ]);
        });

        self::dispatch();
    }
}
