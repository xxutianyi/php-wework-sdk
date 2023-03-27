<?php

namespace PHPWeworkSDK\Abstract;

use PHPWeworkSDK\ErrorCode\ClientError;
use PHPWeworkSDK\Exception\CallException;

abstract class BaseModel
{
    protected array $requiredAttributes;
    protected array $fullAttributes;

    /**
     * @throws CallException
     */
    protected function checkRequiredAttribute(array $modelArray): void
    {
        foreach ($this->requiredAttributes as $key) {
            if (!key_exists($key, $modelArray))
                throw new CallException(ClientError::MODEL_TO_FEW_ATTRIBUTES);
        }

        foreach ($modelArray as $key => $value) {
            if (!key_exists($key, $this->fullAttributes))
                throw new CallException(ClientError::MODEL_TO_MANY_ATTRIBUTES);
        }
    }

    /**
     * @param array|null $modelArray
     * @throws CallException
     */
    public function __construct(array $modelArray = null)
    {
        if (!empty($modelArray)) {
            $this->checkRequiredAttribute($modelArray);

            foreach ($modelArray as $key => $value) {
                $this->$key = $value;
            }
        }

    }

    public function toArray(): array
    {
        return json_decode(json_encode($this), true);
    }

    public function __toString(): string
    {
        return json_encode($this);
    }

    public function __get(string $name)
    {
        return $this->$name;
    }

    public function __set(string $name, $value): void
    {
        $this->$name = $value;
    }
}
