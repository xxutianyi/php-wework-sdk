<?php
/*
 * Copyright (c) XuTianyi 2023.
 * Email: xutianyi12@outlook.com.
 * Github: https://github.com/xxutianyi
 */

namespace PHPWeworkSDK\Abstract;

use GuzzleHttp\Exception\GuzzleException;
use PHPWeworkSDK\Api\Endpoint;
use PHPWeworkSDK\ErrorCode\RemoteError;
use PHPWeworkSDK\Exception\CallException;
use PHPWeworkSDK\Exception\RemoteException;
use SimpleRequest\Request;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

abstract class Api
{
    const BASE_URL = "https://qyapi.weixin.qq.com/cgi-bin";

    protected array $builtinApp = [
        'CONTACT',
        'DAIL',
        'CHECKIN',
        'MEETING_ROOM',
        'KF',
    ];
    protected FilesystemAdapter $cache;

    protected string $cacheKeyPrefix = "php_wework_sdk.xxutianyi.access_token.";
    protected string $cacheItemKey;
    protected string $accessToken;

    public function __construct()
    {
        $this->cache = new FilesystemAdapter();
    }

    /**
     * @param Endpoint $endpoint
     * @param string $method
     * @param array $query
     * @param array $params
     * @param bool $auth
     * @return array
     * @throws CallException
     * @throws GuzzleException
     * @throws RemoteException
     */
    protected function request(Endpoint $endpoint, string $method, array $query = [], array $params = [], bool $auth = true): array
    {
        $url = $this->makeUrl($endpoint);
        $headers = [];

        if ($auth) {
            $query[] = ['access_token' => $this->accessToken];
        }

        if ($method == "GET") {
            $response = Request::get($url, $query, $headers);
        } elseif ($method == "POST") {
            $response = Request::post($url, $query, $params, [], $headers);
        } else {
            throw new CallException();
        }

        $response = json_decode($response->getContents(), true);

        $this->handleError($response);

        return $response;

    }

    private function makeUrl(Endpoint $endpoint): string
    {
        return self::BASE_URL . $endpoint->value;
    }

    /**
     * @param array $response
     * @return void
     * @throws RemoteException
     */
    private function handleError(array $response): void
    {
        if (key_exists('errcode', $response)) {
            $errorCode = (int)$response['errcode'];
            if ($errorCode) {
                throw new RemoteException(RemoteError::tryFrom($errorCode));
            }
        } else {
            throw new RemoteException();
        }
    }

    protected abstract function getAccessToken(): string;

    protected abstract function refreshAccessToken(): string;

}

