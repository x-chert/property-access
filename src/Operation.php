<?php

declare(strict_types=1);

namespace Xchert\PropertyAccess;

use Xchert\PropertyAccess\Exception\InvalidOperationException;

enum Operation: string
{
    case Get = 'get';
    case Set = 'set';
    case Push = 'push';
    case Merge = 'merge';
    case Collect = 'collect';
    case Has = 'has';

    /**
     * @throws InvalidOperationException
     */
    public static function getByValue(string $value): Operation
    {
        foreach(self::cases() as $operation) {
            if($operation->value === $value) {
                return $operation;
            }
        }

        throw new InvalidOperationException($value);
    }
}
