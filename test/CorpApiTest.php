<?php
/*
 * Copyright (c) XuTianyi 2023.
 * Email: xutianyi12@outlook.com.
 * Github: https://github.com/xxutianyi
 */

namespace PHPWeWorkSDK\Tests;

use PHPUnit\Framework\TestCase;
use PHPWeworkSDK\Api\CorpApi;
use Symfony\Component\Dotenv\Dotenv;

class CorpApiTest extends TestCase
{
    public function testSDK()
    {

        $dotenv = new Dotenv();

        $dotenv->load(__DIR__ . '/.env');

        $corpID = $_ENV['CORP_ID'];
        $agentID = (int)$_ENV['AGENT_ID'];
        $secret = $_ENV['SECRET'];

        $sdk = new CorpApi($agentID, $corpID, $secret);

        $this->assertInstanceOf(
            CorpApi::class,
            $sdk,
        );
    }

}
