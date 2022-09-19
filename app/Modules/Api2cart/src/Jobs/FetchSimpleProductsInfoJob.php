<?php

namespace App\Modules\Api2cart\src\Jobs;

use App\Modules\Api2cart\src\Exceptions\RequestException;
use App\Modules\Api2cart\src\Models\Api2cartConnection;
use App\Modules\Api2cart\src\Models\Api2cartSimpleProduct;
use App\Modules\Api2cart\src\Services\Api2cartService;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class FetchSimpleProductsInfoJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Execute the job.
     *
     * @return void
     * @throws GuzzleException
     * @throws RequestException
     */
    public function handle()
    {
        Api2cartConnection::query()
            ->get()
            ->each(function (Api2cartConnection $conn) {
                Api2cartSimpleProduct::query()
                    ->where(['api2cart_connection_id' => $conn->id])
                    ->whereRaw('(last_fetched_at IS NULL OR last_fetched_data IS NULL)')
                    ->chunkById(20, function ($chunk) use ($conn) {
                        $this->fetchApi2cartData($conn, $chunk->pluck('api2cart_product_id')->toArray());
                    });
            });
    }

    /**
     * @throws RequestException
     * @throws GuzzleException
     */
    private function fetchApi2cartData(Api2cartConnection $conn, array $product_ids)
    {
        $response = Api2cartService::getProductsList($conn, $product_ids);

        if ($response->isNotSuccess()) {
            throw new RequestException(implode(' ', [
                $response->getReturnCode(),
                $response->getReturnMessage()
            ]));
        }

        $productRecords = data_get($response->collect(), 'result.product');

        collect($productRecords)
            ->each(function ($product) use ($conn) {
                Api2cartSimpleProduct::query()
                    ->where([
                        'api2cart_product_id' => $product['id'],
                        'api2cart_connection_id' => $conn->id,
                    ])
                    ->first()
                    ->update([
                        'is_in_sync' => null,
                        'last_fetched_data' => Api2cartService::transformProduct($product, $conn)
                    ]);
            });
    }
}
