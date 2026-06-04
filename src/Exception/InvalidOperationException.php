<?php

namespace Xchert\PropertyAccess\Exception;

class InvalidOperationException extends \Exception
{
    public function __construct(string $operation)
    {
        parent::__construct(\sprintf('%s is not a valid operation.', $operation));
    }
}