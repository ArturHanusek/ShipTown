<?php

namespace App\Modules\BoxTop\src\Services;

use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\ProductAlias;
use App\Modules\BoxTop\src\Api\ApiClient;
use App\Modules\BoxTop\src\Api\ApiResponse;
use App\Modules\BoxTop\src\Exceptions\ProductOutOfStockException;
use App\Modules\BoxTop\src\Models\WarehouseStock;
use Carbon\Carbon;
use GuzzleHttp\Exception\ClientException;
use function Aws\map;

/**
 *
 */
class BoxTopService
{
    /**
     * @param Order $order
     * @return ApiResponse
     */
    public static function postOrder(Order $order): ApiResponse
    {
        self::refreshBoxTopWarehouseStock();

        $data = self::convertToBoxTopFormat($order);

        try {
            return self::apiClient()->createWarehousePick($data);
        } catch (ClientException $exception) {
            ray($exception->getResponse()->getBody()->getContents());
            throw $exception;
        }
    }

    /**
     * @return ApiClient
     */
    public static function apiClient(): ApiClient
    {
        return new ApiClient();
    }

    /**
     * @param Order $order
     * @return array
     */
    private static function convertToBoxTopFormat(Order $order): array
    {
        $pickItems = $order->orderProducts->map(function (OrderProduct $orderProduct) {
            $apiClient = new ApiClient();
            $apiClient->getSkuQuantity($orderProduct->sku_ordered);

            $possibleSkus = [$orderProduct->sku_ordered];
            $possibleSkus += [$orderProduct->product->sku];
            $possibleSkus += $orderProduct->product->aliases()
                ->get('alias')
                ->map(function (ProductAlias $alias) {
                    return $alias->alias;
                })->toArray();

            dd($possibleSkus);

            /** @var WarehouseStock $warehouseStock */
            $warehouseStock = WarehouseStock::query()
                ->whereIn('SKUNumber', $possibleSkus)
                ->where('Available', '>=', $orderProduct->quantity_ordered)
                ->first();

            if ($warehouseStock === null) {
                throw new ProductOutOfStockException('Insufficient quantity available: '.$orderProduct->sku_ordered);
            }

            return [
                "Warehouse"     => $warehouseStock->Warehouse,
                "SKUGroupID"    => null,
                "SKUNumber"     => $warehouseStock->SKUNumber,
                "SKUName"       => $warehouseStock->SKUName,
                "Quantity"      => $orderProduct->quantity_ordered,
                "Add1"          => "",
                "Add2"          => "",
                "Add3"          => "",
                "Add4"          => "",
                "Add5"          => "",
                "Add6"          => "",
                "Comments"      => ""
            ];
        })->toArray();

        return [
            "DeliveryCompanyName"   => $order->shippingAddress->company,
            "DeliveryAddress1"      => $order->shippingAddress->address1,
            "DeliveryAddress2"      => $order->shippingAddress->address2,
            "DeliveryCity"          => $order->shippingAddress->city,
            "DeliveryCounty"        => $order->shippingAddress->state_code,
            "DeliveryPostCode"      => $order->shippingAddress->postcode,
            "DeliveryCountry"       => $order->shippingAddress->country_code,
            "DeliveryPhone"         => $order->shippingAddress->phone,
            "DeliveryContact"       => $order->shippingAddress->full_name . ' ' .$order->shippingAddress->email,
            "OutboundRef"           => "WEB_". $order->order_number,
            "ReleaseDate"           => Carbon::today(),
            "DeliveryDate"          => "",
            "DeliveryTime"          => "",
            "Haulier"               => "",
            "PickItems"             => $pickItems,
            "BranchID"              => 513,
            "CustomerID"            => "BELLABAB",
            "NOP"                   => 1,
            "Weight"                => 1,
            "Cube"                  => 1,
            "CustRef"               => "WEB_". $order->order_number,
            "Remarks"               => ""
        ];
    }

    public static function refreshBoxTopWarehouseStock()
    {
        $response = BoxTopService::apiClient()->getStockCheckByWarehouse();
        $stockRecords = collect($response->toArray());

        $stockRecords = $stockRecords->map(function ($record) {
            $record['Attributes'] = json_encode($record['Attributes']);
            return $record;
        });

        WarehouseStock::query()->delete();
        WarehouseStock::query()->insert($stockRecords->toArray());
    }
}
