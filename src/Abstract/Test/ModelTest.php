<?php

namespace PHPWeworkSDK\Abstract\Test;

use PHPUnit\Framework\TestCase;

abstract class ModelTest extends TestCase
{
    public abstract function testToArray();

    public abstract function testToModel();

    public abstract function testToModelFailed();
}
