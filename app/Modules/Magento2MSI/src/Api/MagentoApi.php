<?php

namespace App\Modules\Magento2MSI\src\Api;

use App\Modules\Magento2MSI\src\Models\Magento2msiConnection;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;

class MagentoApi
{
    public static function getModules(Magento2msiConnection $connection): ?Response
    {
        return Client::get($connection->api_access_token, $connection->base_url . '/modules', [
            'searchCriteria' => [
                'filterGroups' => [
                    [
                        'filters' => [
//                            [
//                                'field' => '',
//                                'value' => '',
//                                'condition_type' => 'in'
//                            ]
                        ]
                    ],
                ]
            ],
        ]);
    }

    public static function getInventorySources(Magento2msiConnection $connection): ?Response
    {
        return Client::get($connection->api_access_token, $connection->base_url . '/inventory/sources', [
            'searchCriteria' => [
                'filterGroups' => [
                    [
                        'filters' => [
//                            [
//                                'field' => '',
//                                'value' => '',
//                                'condition_type' => 'in'
//                            ]
                        ]
                    ],
                ]
            ],
        ]);
    }

    public static function getOrders($token, $parameters = []): ?Response
    {
        return Client::get($token, '/orders', $parameters);
    }

    public static function postProducts($token, $sku, $name): ?Response
    {
        return Client::post($token, '/products', [
            'products' => [
                [
                    'sku' => $sku,
                    'name' => $name,
                ],
            ],
        ]);
    }

    public static function postProductsSpecialPrice($token, $sku, $store_id, $price, $price_from, $price_to): ?Response
    {
        return Client::post($token, '/products/special-price', [
            'prices' => [
                [
                    'sku' => $sku,
                    'store_id' => $store_id,
                    'price' => $price,
                    'price_from' => $price_from,
                    'price_to' => $price_to,
                ]
            ]
        ]);
    }

    public static function postProductsSpecialPriceInformation($token, $sku): ?Response
    {
        return Client::post($token, '/products/special-price-information', [
            'skus' => Arr::wrap($sku)
        ]);
    }

    public static function postProductsBasePricesInformation($token, $sku): ?Response
    {
        return Client::post($token, '/products/base-prices-information', [
            'skus' => Arr::wrap($sku)
        ]);
    }

    public static function putStockItems($token, $sku, $params): ?Response
    {
        return Client::put($token, '/products/'.$sku.'/stockItems/0', [
            'stockItem' => $params,
        ]);
    }

    public static function getStockItems($token, $sku): ?Response
    {
        return Client::get($token, '/stockItems/'.$sku);
    }

    public static function getInventorySourceItems(Magento2msiConnection $connection, $skuList): ?Response
    {
        $skus = collect($skuList)->implode(',');

        return Client::get($connection->api_access_token, $connection->base_url . '/inventory/source-items', [
            'searchCriteria' => [
                'filterGroups' => [
                    [
                        'filters' => [
                            [
                                'field' => 'sku',
                                'value' => $skus,
                                'condition_type' => 'in'
                            ]
                        ]
                    ],
                    [
                        'filters' => [
                            [
                                'field' => 'source_code',
                                'value' => $connection->store_code,
                                'condition_type' => 'in'
                            ]
                        ]
                    ]
                ]
            ],
        ]);
    }

//    public static function postInventorySourceItems($token, $sku, $storeCode, $quantity): ?Response
    public static function postInventorySourceItems($token, $sourceItems): ?Response
    {
        return Client::post($token, '/inventory/source-items', [
            'sourceItems' => [
                [
                    'source_code' => $storeCode,
                    'sku' => $sku,
                    'quantity' => $quantity,
                    'status' => 1,
                ]
            ],
        ]);
    }

    public static function postProductsBasePrices($token, string $sku, float $price, int $store_id): ?Response
    {
        return Client::post($token, '/products/base-prices', [
            'prices' => [
                [
                'sku' => $sku,
                'price' => $price,
                'store_id' => $store_id,
                ]
            ]
        ]);
    }
}
