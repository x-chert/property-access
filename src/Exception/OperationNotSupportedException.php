<?php

namespace Xchert\PropertyAccess\Exception;

use Xchert\PropertyAccess\Operation;

class OperationNotSupportedException extends \Exception
{
    public function __construct(Operation $operation)
    {
        parent::__construct(\sprintf('Operation %s is not supported.', $operation->value));
    }
}
