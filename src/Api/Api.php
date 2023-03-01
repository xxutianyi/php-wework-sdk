<?php
/*
 * Copyright (c) XuTianyi 2023.
 * Email: xutianyi12@outlook.com.
 * Github: https://github.com/xxutianyi
 */

namespace xXutianyi\PhpWeworkSdk\Api;

use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use xXutianyi\PhpWeworkSdk\ErrorCode\RemoteError;
use xXutianyi\PhpWeworkSdk\Exception\CallException;
use xXutianyi\PhpWeworkSdk\Exception\RemoteException;
use xXutianyi\PhpWeworkSdk\Utils\Request;

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

    public function __construct()
    {
        $this->cache = new FilesystemAdapter();
    }

    /**
     * @param string $endpoint
     * @param string $method
     * @param array $query
     * @param array $params
     * @param string $accessToken
     * @return string
     * @throws GuzzleException
     */
    public function request(string $endpoint, string $method, array $query = [], array $params = [], string $accessToken = ""): array
    {
        $url = $this->makeUrl($endpoint);
        $headers = [];

        if ($method == "GET") {
            $response = Request::get($url, $query, $headers);
        } elseif ($method == "POST") {
            $response = Request::post($url, $query, $params, [], $headers);
        } else {
            throw new CallException('Method Not Allowed');
        }

        $response = json_decode($response->getContents(), true);

        $this->handleError($response);

        return $response;

    }

    private function makeUrl($endpoint): string
    {
        return self::BASE_URL . $endpoint;
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
                $error = RemoteError::from($errorCode);
                throw new RemoteException($error->name, $error->value);
            }
        } else {
            throw new RemoteException('RemoteError Response Error:' . json_encode($response));
        }
    }

    protected abstract function getAccessToken(): string;

    protected abstract function refreshAccessToken(): string;

}

