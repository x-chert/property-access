<?php

namespace Xchert\PropertyAccess;

use Xchert\PropertyAccess\Exception\InvalidInputException;
use Xchert\PropertyAccess\Exception\InvalidPathException;
use Xchert\PropertyAccess\Exception\OperationNotSupportedException;
use Xchert\PropertyAccess\Exception\PropertyNotFoundException;
use Xchert\Util\Exception\InvalidTypeException;
use Xchert\Util\Reflection;
use Xchert\Util\Type;

class ObjectAccessor extends Accessor
{
    public const string ID = 'object';

    public function supports(Operation $operation, mixed $value): bool
    {
        return \is_object($value) && !\in_array($operation, [Operation::Push, Operation::Collect]);
    }

    /**
     * @throws PropertyNotFoundException
     * @throws \ReflectionException
     * @throws InvalidTypeException
     */
    public function get(string $field, mixed $data, AccessContext $context): mixed
    {
        Type::validate($data, Type::OBJECT);

        $reflectionObject = new \ReflectionObject($data);

        $method = Reflection::getMethod($reflectionObject, \sprintf('get%s', \ucfirst($field)));

        if ($method !== null) {
            return $method->invoke($data);
        }

        $property = Reflection::getProperty($reflectionObject, $field);

        if ($property === null) {
            if ($context->hasFlags(Flags::STRICT)) {
                throw new PropertyNotFoundException($context->getPath());
            }

            return null;
        }

        if ($property->hasType() && !$property->isInitialized($data)) {
            return null;
        }

        return $property->getValue($data);
    }

    /**
     * @throws PropertyNotFoundException
     * @throws InvalidTypeException
     * @throws \ReflectionException
     */
    public function set(string $field, mixed &$data, mixed $value, AccessContext $context): void
    {
        Type::validate($data, Type::OBJECT);

        $reflectionObject = new \ReflectionObject($data);
        $method = Reflection::getMethod($reflectionObject, \sprintf('set%s', \ucfirst($field)));

        if ($method !== null) {
            $method->invoke($data, $value);

            return;
        }

        $property = Reflection::getProperty($reflectionObject, $field);

        if ($property === null) {
            if ($context->hasFlags(Flags::STRICT)) {
                throw new PropertyNotFoundException($context->getPath());
            }

            return;
        }

        $property->setValue($data, $value);
    }

    /**
     * @throws PropertyNotFoundException
     * @throws OperationNotSupportedException
     * @throws InvalidTypeException
     * @throws InvalidPathException
     * @throws InvalidInputException
     */
    public function merge(mixed &$data, mixed $value, AccessContext $context): void
    {
        Type::validate($data, Type::OBJECT);

        parent::merge($data, $value, $context);
    }

    public function push(mixed &$data, mixed $value, AccessContext $context): void
    {
        throw new OperationNotSupportedException(Operation::Push);
    }

    public function collect(mixed $data, AccessContext $context): array
    {
        throw new OperationNotSupportedException(Operation::Collect);
    }

    /**
     * @throws InvalidTypeException
     */
    public function has(string $field, mixed $data, AccessContext $context): bool
    {
        Type::validate($data, Type::OBJECT);

        $reflectionObject = new \ReflectionObject($data);

        return Reflection::getProperty($reflectionObject, $field) !== null;
    }
}