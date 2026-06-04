<?php

declare(strict_types=1);

namespace Xchert\PropertyAccess;

use Xchert\PropertyAccess\Exception\InvalidInputException;
use Xchert\PropertyAccess\Exception\InvalidPathException;
use Xchert\PropertyAccess\Exception\OperationNotSupportedException;
use Xchert\PropertyAccess\Exception\PropertyNotFoundException;

abstract class Accessor
{
    abstract public function supports(Operation $operation, mixed $value): bool;

    /**+
     * @throws OperationNotSupportedException
     * @throws PropertyNotFoundException
     */
    abstract public function get(string $field, mixed $data, AccessContext $context): mixed;

    /**+
     * @throws OperationNotSupportedException
     * @throws PropertyNotFoundException
     */
    abstract public function set(string $field, mixed &$data, mixed $value, AccessContext $context): void;

    /**+
     * @throws OperationNotSupportedException
     */
    abstract public function push(mixed &$data, mixed $value, AccessContext $context): void;

    /**+
     * @throws OperationNotSupportedException
     */
    abstract public function collect(mixed $data, AccessContext $context): array;

    /**+
     * @throws OperationNotSupportedException
     */
    abstract public function has(string $field, mixed $data, AccessContext $context): bool;

    /**
     * @throws InvalidPathException
     * @throws InvalidInputException
     * @throws OperationNotSupportedException
     * @throws PropertyNotFoundException
     */
    public function access(?string $field, mixed &$data, mixed $value, AccessContext $context): mixed
    {
        switch($context->getOperation()) {
            case Operation::Get:
                return $this->get($field, $data, $context);

            case Operation::Set:
                $this->set($field, $data, $value, $context);
                return null;
            case Operation::Push:
                $this->push($data, $value, $context);
                return null;

            case Operation::Merge:
                $this->merge($data, $value, $context);
                return null;

            case Operation::Collect:
                return $this->collect($data, $context);

            case Operation::Has:
                return $this->has($field, $data, $context);
        }

        throw new OperationNotSupportedException($context->getOperation());
    }

    /**
     * @throws InvalidPathException
     * @throws InvalidInputException
     * @throws OperationNotSupportedException
     * @throws PropertyNotFoundException
     */
    public function merge(mixed &$data, mixed $value, AccessContext $context): void
    {
        if(!$this->supports(Operation::Merge, $data)) {
            throw new OperationNotSupportedException(Operation::Merge);
        }

        $propertyAccessor = $context->getPropertyAccessor();

        foreach(Util::valueToMerge($value) as $key => $valueToMerge) {
            $getContext = $context->subContext(Operation::Get, new Path([$key]));
            $getContext->removeFlag(Flags::STRICT);

            $dataValue = $this->get($key, $data, $getContext);

            if(Util::isMergeable($dataValue) && Util::isMergeable($valueToMerge)) {
                $propertyAccessor->write(new Path([]), $dataValue, $valueToMerge, $context->subContext(Operation::Merge, new Path([$key])));
                $this->set($key, $data, $dataValue, $context->subContext(Operation::Set, new Path([$key])));

                continue;
            }

            $this->set($key, $data, $valueToMerge, $context->subContext(Operation::Set, new Path([$key])));
        }
    }
}
