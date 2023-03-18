<?php
/*
 * Copyright (c) XuTianyi 2023.
 * Email: xutianyi12@outlook.com.
 * Github: https://github.com/xxutianyi
 */

namespace PHPWeWorkSDK\Tests;

use PHPWeworkSDK\Abstract\Test\ModelTest;
use PHPWeworkSDK\Exception\CallException;
use PHPWeworkSDK\Model\User;

class UserTest extends ModelTest
{
    public function testToModel()
    {

        $attrs = [
            'userid' => '123',
            'name' => 'TestName',
            'department' => '0',
            'alias' => 'Test',
        ];

        $user = new User($attrs);

        $this->expectOutputString(json_encode($attrs));

        echo($user);

    }

    public function testToModelFailed()
    {
        $attrs = [
            'userid' => '123',
            'name' => 'TestName',
        ];

        $this->expectException(CallException::class);

        new User($attrs);
    }

    public function testToArray()
    {
        $attrs = [
            'userid' => '123',
            'name' => 'TestName',
            'department' => '0',
            'alias' => 'Test',
        ];

        $user = new User($attrs);

        $this->assertSame($attrs, $user->toArray());
    }

}
