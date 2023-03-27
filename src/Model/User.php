<?php

namespace PHPWeworkSDK\Model;

use PHPWeworkSDK\Abstract\BaseModel;

/**
 * @property $user_id
 * @property $name
 * @property $department
 */
class User extends BaseModel
{
    protected array $requiredAttributes = [
        'userid',
        'name',
        'department',
    ];

    protected array $fullAttributes = [
        'userid',
        'name',
        'department',
    ];
}
