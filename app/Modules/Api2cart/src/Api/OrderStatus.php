<?php


namespace App\Modules\Api2cart\src\Api;

use GuzzleHttp\Exception\GuzzleException;

/**
 *
 */
class OrderStatus
{
    /**
     * @param string $store_key
     * @param array $params
     * @return RequestResponse
     * @throws GuzzleException
     */
    public static function list(string $store_key, array $params = []): RequestResponse
    {
        return Client::GET($store_key, 'order.status.list.json', $params);
    }
}
