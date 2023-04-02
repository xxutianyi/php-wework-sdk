<?php
/*
 * Copyright (c) XuTianyi 2023.
 * Email: xutianyi12@outlook.com.
 * Github: https://github.com/xxutianyi
 */

namespace PHPWeWorkSDK\Tests;

use GuzzleHttp\Exception\GuzzleException;
use PHPUnit\Framework\TestCase;
use PHPWeworkSDK\Api\CorpApi;
use PHPWeworkSDK\Exception\CallException;
use PHPWeworkSDK\Exception\RemoteException;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Dotenv\Dotenv;

class CorpApiTest extends TestCase
{
    private CorpApi $api;
    private string $corpID;
    private string $secret;
    private int $agentID;

    /**
     * @throws RemoteException
     * @throws InvalidArgumentException
     * @throws CallException
     * @throws GuzzleException
     */
    public function __construct(string $name)
    {
        $dotenv = new Dotenv();

        $dotenv->load(__DIR__ . '/.env');

        $this->corpID = $_ENV['CORP_ID'];
        $this->agentID = $_ENV['AGENT_ID'];
        $this->secret = $_ENV['SECRET'];

        $this->api = new CorpApi($this->agentID, $this->corpID, $this->secret);

        parent::__construct($name);
    }

    public function test__construct()
    {
        $this->assertInstanceOf(
            CorpApi::class,
            $this->api,
        );
    }

    public function test_get_access_token()
    {
        $this->assertIsString($this->api->getAccessToken());
    }

    public function test_get_oauth_url()
    {
        $redirectUrl = 'https://abc.com';
        $state = "";
        $type[true] = 'snsapi_privateinfo';
        $type[false] = 'snsapi_base';

        foreach ($type as $key => $value) {
            $this->assertEquals(
                "https://open.weixin.qq.com/connect/oauth2/authorize?appid=$this->corpID&redirect_uri=$redirectUrl&response_type=code&scope=$value&state=$state&agentid=$this->agentID#wechat_redirect",
                $this->api->getOAuthUrl($redirectUrl, $state, $key)
            );
        }
    }



}
