<?php
/*
 * Copyright (c) XuTianyi 2023.
 * Email: xutianyi12@outlook.com.
 * Github: https://github.com/xxutianyi
 */

namespace xXutianyi\PhpWeworkSdk\Exception;

use Exception;
use xXutianyi\PhpWeworkSdk\ErrorCode\ClientError;

class CallException extends Exception
{
    public function __construct(string $message = "", int $code = null, Exception $previous = null)
    {
        if (empty($message)) {
            $message = ClientError::API_PARAMS_ERROR->name;
        }

        if (empty($code)) {
            $code = ClientError::API_PARAMS_ERROR->value;
        }

        parent::__construct($message, $code, $previous);
    }

    public function __toString(): string
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}
