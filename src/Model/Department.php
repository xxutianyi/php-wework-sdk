<?php

namespace PHPWeworkSDK\Model;

use PHPWeworkSDK\Abstract\BaseModel;

/**
 * @property $parentid
 * @property $order
 * @property $name
 * @property $name_en
 * @property $id
 */
class Department extends BaseModel
{
    protected array $requiredAttributes = [
        'name',
        'parentid',
    ];

    protected array $fullAttributes = [
        'name',
        'name_en',
        'parentid',
        'order',
        'id',
    ];

}
