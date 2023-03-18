<?php
/*
 * Copyright (c) XuTianyi 2023.
 * Email: xutianyi12@outlook.com.
 * Github: https://github.com/xxutianyi
 */

namespace PHPWeworkSDK\Exception;

use Exception;
use PHPWeworkSDK\ErrorCode\RemoteError;

class RemoteException extends Exception
{
    public function __construct(RemoteError $error = null, Exception $previous = null)
    {
        $message = "Call Remote Api Error: " . ($error ? $error->name : "Unknown Error");
        $code = $error ? $error->value : 99999;

        parent::__construct($message, $code, $previous);
    }

    public function __toString(): string
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}
