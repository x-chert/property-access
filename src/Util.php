<?php

namespace Xchert\PropertyAccess;

use Xchert\PropertyAccess\Exception\InvalidInputException;
use Xchert\Util\Reflection;

class Util
{
    public const string COLLECTOR_FIELD = '[]';

    public static function hasCollector(Path $path): bool
    {
        foreach($path as $field) {
            if($field === self::COLLECTOR_FIELD) {
                return true;
            }
        }

        return false;
    }

    public static function flatToNested(array $data): array
    {
        $result = [];

        foreach($data as $path => $value) {
            $path = Path::parse($path);

            $pointer = &$result;

            foreach($path as $field) {
                if(!isset($pointer[$field])) {
                    $pointer[$field] = [];
                }

                $pointer = &$pointer[$field];
            }

            $pointer = $value;
        }

        return $result;
    }

    /**
     * @throws InvalidInputException
     */
    public static function valueToMerge(mixed $data): \Generator
    {
        if(!static::isMergeable($data)) {
            throw InvalidInputException::notMergable($data);
        }

        if(\is_iterable($data)) {
            foreach($data as $key => $value) {
                yield $key => $value;
            }

            return;
        }

        $reflectionObject = new \ReflectionObject($data);

        /** @var \ReflectionProperty $reflectionProperty */
        foreach(Reflection::getProperties($reflectionObject) as $reflectionProperty) {
            $name = $reflectionProperty->getName();

            $method = Reflection::getMethod($reflectionObject, \sprintf('get%s', \ucfirst($name)));

            if($method !== null) {
                yield $name => $method->invoke($data);

                continue;
            }

            if($reflectionProperty->hasType() && !$reflectionProperty->isInitialized($data)) {
                yield $name => null;

                continue;
            }

            yield $name => $reflectionProperty->getValue($data);
        }
    }

    public static function isMergeable(mixed $data): bool
    {
        return \is_iterable($data) || \is_object($data);
    }

    public static function isIndexField(string $field): bool
    {
        return $field === (string) (int) $field;
    }

}