<?php

namespace PHPWeworkSDK\Model;

use PHPWeworkSDK\Abstract\BaseModel;

class User extends BaseModel
{
    protected array $requiredAttributes = [
        'userid',
        'name',
        'department',
    ];

    protected array $fullAttributes = [

    ];
}
