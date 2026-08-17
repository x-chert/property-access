<?php

namespace Xchert\PropertyAccess\Exception;

class InvalidPathException extends \Exception
{
    public static function invalidPathElement(mixed $element): self
    {
        return new self(
            \sprintf('Element of type %s cannot be used as path field.', \get_debug_type($element)),
        );
    }

    public static function emptyField(?int $position = null): self
    {
        $message = 'Field ';

        if ($position !== null) {
            $message .= \sprintf('at position %s ', $position);
        }

        $message .= 'cannot be empty.';

        return new self($message);
    }

    public static function emptyPath(): self
    {
        return new self('Path cannot be empty.');
    }
}
