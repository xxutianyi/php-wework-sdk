<?php
/*
 * Copyright (c) XuTianyi 2023.
 * Email: xutianyi12@outlook.com.
 * Github: https://github.com/xxutianyi
 */

namespace xXutianyi\PhpWeworkSdk\Utils;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\StreamInterface;

class Request
{

    /**
     * @param string $url
     * @param array $query
     * @param array $headers
     * @return StreamInterface
     * @throws GuzzleException
     */
    public static function get(string $url, array $query = [], array $headers = []): StreamInterface
    {
        $client = new Client();
        return $client->get(
            $url,
            [
                'headers' => $headers,
                'query' => $query,
            ]
        )->getBody();
    }

    /**
     * @param string $url
     * @param array $query
     * @param array $params
     * @param array $form
     * @param array $headers
     * @return StreamInterface
     * @throws GuzzleException
     */
    public static function post(string $url, array $query = [], array $params = [], array $form = [], array $headers = []): StreamInterface
    {
        $client = new Client();
        return $client->post(
            $url,
            [
                'headers' => $headers,
                'query' => $query,
                'json' => $params,
                'multipart' => $form,
            ]
        )->getBody();
    }

    /**
     * @param string $url
     * @param array $query
     * @param array $params
     * @param array $headers
     * @return StreamInterface
     * @throws GuzzleException
     */
    public static function put(string $url, array $query = [], array $params = [], array $headers = []): StreamInterface
    {
        $client = new Client();
        return $client->put(
            $url,
            [
                'headers' => $headers,
                'query' => $query,
                'json' => $params,
            ]
        )->getBody();
    }

    /**
     * @param string $url
     * @param array $query
     * @param array $headers
     * @return StreamInterface
     * @throws GuzzleException
     */
    public static function delete(string $url, array $query = [], array $headers = []): StreamInterface
    {
        $client = new Client();
        return $client->delete(
            $url,
            [
                'headers' => $headers,
                'query' => $query,
            ]
        )->getBody();
    }
}
