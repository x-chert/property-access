<?php

declare(strict_types=1);

namespace Xchert\PropertyAccess;

use Xchert\PropertyAccess\Exception\InvalidInputException;
use Xchert\PropertyAccess\Exception\InvalidPathException;
use Xchert\PropertyAccess\Exception\OperationNotSupportedException;
use Xchert\PropertyAccess\Exception\PropertyNotFoundException;
use Xchert\Util\Exception\InvalidTypeException;
use Xchert\Util\Pod\Collection;
use Xchert\Util\Type;

class CollectionAccessor extends Accessor
{
    public const string ID = 'collection';

    public function supports(Operation $operation, mixed $value): bool
    {
        return $value instanceof Collection;
    }

    /**
     * @throws PropertyNotFoundException
     * @throws InvalidTypeException
     */
    public function get(string $field, mixed $data, AccessContext $context): mixed
    {
        Type::validate($data, Collection::class);

        if (!$data->has($field)) {
            if ($context->hasFlags(Flags::STRICT)) {
                throw new PropertyNotFoundException($context->getPath());
            }

            return null;
        }

        return $data->get($field);
    }

    /**
     * @throws InvalidTypeException
     */
    public function set(string $field, mixed &$data, mixed $value, AccessContext $context): void
    {
        Type::validate($data, Collection::class);

        $data->add($value, $field);
    }

    /**
     * @throws InvalidTypeException
     */
    public function push(mixed &$data, mixed $value, AccessContext $context): void
    {
        Type::validate($data, Collection::class);

        $data->add($value);
    }

    /**
     * @throws InvalidTypeException
     */
    public function collect(mixed $data, AccessContext $context): array
    {
        Type::validate($data, Collection::class);

        return $data->toArray();
    }

    /**
     * @throws InvalidTypeException
     */
    public function has(string $field, mixed $data, AccessContext $context): bool
    {
        Type::validate($data, Collection::class);

        return $data->has($field);
    }

    /**
     * @throws InvalidTypeException
     * @throws InvalidPathException
     * @throws InvalidInputException
     * @throws PropertyNotFoundException
     * @throws OperationNotSupportedException
     * @throws \ReflectionException
     */
    public function merge(mixed &$data, mixed $value, AccessContext $context): void
    {
        Type::validate($data, Collection::class);

        foreach (Util::valueToMerge($value) as $key => $valueToMerge) {
            $key = (string)$key;

            $getContext = $context->subContext(Operation::Get, new Path([$key]));
            $getContext->removeFlag(Flags::STRICT);

            $dataValue = $this->get($key, $data, $getContext);

            if (Util::isMergeable($dataValue) && Util::isMergeable($valueToMerge)) {
                $context->getPropertyAccessor()->write(new Path([]), $dataValue, $valueToMerge, $context->subContext(Operation::Merge, new Path([$key])));
                $this->set($key, $data, $dataValue, $context);

                continue;
            }

            if (Util::isIndexField($key) && !$context->hasFlags(ArrayAccessor::MERGE_OVERWRITE_NUMERIC)) {
                $this->push($data, $valueToMerge, $context);
            } else {
                $this->set($key, $data, $valueToMerge, $context);
            }
        }
    }
}
