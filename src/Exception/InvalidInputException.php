<?php

namespace Xchert\PropertyAccess\Exception;

class InvalidInputException extends \Exception
{
    public static function notMergable(mixed $data): self
    {
        return new self(\sprintf('Value of type %s is not mergable.', \get_debug_type($data)));
    }

    public static function keyNotNumeric(string $key): self
    {
        return new self(\sprintf('Key %s is not numeric.', $key));
    }
}
