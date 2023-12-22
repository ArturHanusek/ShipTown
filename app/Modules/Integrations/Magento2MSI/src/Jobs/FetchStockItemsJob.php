<?php

namespace App\Modules\Integrations\Magento2MSI\src\Jobs;

use App\Abstracts\UniqueJob;
use App\Modules\Integrations\Magento2MSI\src\Api\MagentoApi;
use App\Modules\Integrations\Magento2MSI\src\Models\MagentoConnection;
use App\Modules\Integrations\Magento2MSI\src\Models\MagentoProduct;
use Exception;
use Illuminate\Support\Collection;

class FetchStockItemsJob extends UniqueJob
{
    public function handle(): void
    {
        MagentoConnection::query()->get()
            ->each(function (MagentoConnection $connection) {
                MagentoProduct::query()
                    ->whereRaw('IFNULL(exists_in_magento, 1) = 1')
                    ->whereNull('stock_items_fetched_at')
                    ->orWhereNull('stock_items_raw_import')
                    ->chunkById(100, function (Collection $products) use ($connection) {
                        try {
                            $skuList = $products->map(function (MagentoProduct $product) {
                                return $product->product->sku;
                            });
                            $productsToSave = MagentoApi::getInventorySourceItems(
                                $connection->api_access_token,
                                $connection->store_code,
                                $skuList
                            );
                        } catch (Exception $exception) {
                            report($exception);
                        }
                    });
            });
    }
}
