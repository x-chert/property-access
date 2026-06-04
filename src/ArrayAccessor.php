<?php

namespace Xchert\PropertyAccess;

use Xchert\PropertyAccess\Exception\PropertyNotFoundException;
use Xchert\Util\Type;

class ArrayAccessor extends Accessor
{
    public const string ID = 'array';

    public const string MERGE_OVERWRITE_NUMERIC = 'merge_overwrite_numeric';

    public function supports(Operation $operation, mixed $value): bool
    {
        return \is_array($value);
    }

    public function get(string $field, mixed $data, AccessContext $context): mixed
    {
        Type::validate($data, Type::ARRAY);

        if(!\array_key_exists($field, $data)) {
            if($context->hasFlags(Flags::STRICT)) {
                throw new PropertyNotFoundException($context->getPath());
            }

            return null;
        }

        return $data[$field];
    }

    public function set(string $field, mixed &$data, mixed $value, AccessContext $context): void
    {
        Type::validate($data, Type::ARRAY);

        $data[$field] = $value;
    }

    public function push(mixed &$data, mixed $value, AccessContext $context): void
    {
        Type::validate($data, Type::ARRAY);

        $data[] = $value;
    }

    public function collect(mixed $data, AccessContext $context): array
    {
        Type::validate($data, Type::ARRAY);

        return $data;
    }

    public function has(string $field, mixed $data, AccessContext $context): bool
    {
        Type::validate($data, Type::ARRAY);

        return \array_key_exists($field, $data);
    }

    public function merge(mixed &$data, mixed $value, AccessContext $context): void
    {
        Type::validate($data, Type::ARRAY);

        foreach(Util::valueToMerge($value) as $key => $valueToMerge) {
            $getContext = $context->subContext(Operation::Get, new Path([$key]));
            $getContext->removeFlag(Flags::STRICT);

            $dataValue = $this->get((string) $key, $data, $getContext);

            if(Util::isMergeable($dataValue) && Util::isMergeable($valueToMerge)) {
                $context->getPropertyAccessor()->write(new Path([]), $dataValue, $valueToMerge, $context->subContext(Operation::Merge, new Path([$key])));
                $this->set($key, $data, $dataValue, $context);

                continue;
            }

            if(Util::isIndexField($key) && !$context->hasFlags(self::MERGE_OVERWRITE_NUMERIC)) {
                $this->push($data, $valueToMerge, $context);
            } else {
                $this->set($key, $data, $valueToMerge, $context);
            }
        }
    }
}