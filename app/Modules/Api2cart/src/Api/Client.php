<?php

namespace App\Modules\Api2cart\src\Api;

use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Support\Facades\Log;

class Client
{
    /**
     * @param string $store_key
     * @param string $uri
     * @param array $params
     *
     * @return RequestResponse
     */
    public static function GET(string $store_key, string $uri, array $params): RequestResponse
    {
        $query = array_merge($params, [
            'api_key'   => self::getApiKey(),
            'store_key' => $store_key,
        ]);

        $response = new RequestResponse(
            self::getGuzzleClient()->get($uri, [
                'query' => $query
            ])
        );

        // hide sensitive information
        $query['api_key'] = '***';
        $query['store_key'] = '***';

        Log::debug('API2CART GET', [
            'success'           => $response->isSuccess(),
            'uri'               => $uri,
            'response_message'  => $response->asArray(),
            'response_code'     => $response->getResponseRaw()->getStatusCode(),
            'query'             => $query,
        ]);

        return $response;
    }

    /**
     * @param string $store_key
     * @param string $uri
     * @param array  $data
     *
     * @return RequestResponse
     */
    public static function POST(string $store_key, string $uri, array $data): RequestResponse
    {
        $query = [
            'api_key'   => self::getApiKey(),
            'store_key' => $store_key,
        ];

        $response = new RequestResponse(
            self::getGuzzleClient()->post($uri, [
                'query' => $query,
                'json'  => $data,
            ])
        );

        // hide sensitive information
        $query['api_key'] = '***';
        $query['store_key'] = '***';

        Log::debug('API2CART POST', [
            'success'           => $response->isSuccess(),
            'uri'               => $uri,
            'response_message'  => $response->asArray(),
            'response_code'     => $response->getResponseRaw()->getStatusCode(),
            'query'             => $query,
            'json'              => $data,
        ]);

        return $response;
    }

    /**
     * @param string $store_key
     * @param string $uri
     * @param array  $params
     *
     * @return RequestResponse
     */
    public static function DELETE(string $store_key, string $uri, array $params): RequestResponse
    {
        $query = [
            'api_key'   => self::getApiKey(),
            'store_key' => $store_key,
        ];

        $query = array_merge($query, $params);

        $response = self::getGuzzleClient()->delete($uri, ['query' => $query]);

        return new RequestResponse($response);
    }

    /**
     * @return GuzzleClient
     */
    public static function getGuzzleClient(): GuzzleClient
    {
        return new GuzzleClient([
            'base_uri'   => 'https://api.api2cart.com/v1.1/',
            'timeout'    => 60,
            'exceptions' => false,
        ]);
    }

    /**
     * @return string
     */
    public static function getApiKey(): string
    {
        return config('app.api2cart_api_key');
    }
}
