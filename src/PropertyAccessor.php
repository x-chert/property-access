<?php

declare(strict_types=1);

namespace Xchert\PropertyAccess;

use Xchert\PropertyAccess\Exception\InvalidInputException;
use Xchert\PropertyAccess\Exception\InvalidPathException;
use Xchert\PropertyAccess\Exception\NotAccessableException;
use Xchert\PropertyAccess\Exception\OperationNotSupportedException;
use Xchert\PropertyAccess\Exception\PropertyNotFoundException;

class PropertyAccessor
{
    /** @var array<string, array<string, int|Accessor>> */
    private array $accessors = [];

    public function registerAccessor(Accessor $accessor, string $id, int $priority): void
    {
        $record = ['priority' => $priority, 'accessor' => $accessor];
        $this->accessors[$id] = $record;

        \uasort(
            $this->accessors,
            function (array $a, array $b): int {
                return $b['priority'] <=> $a['priority'];
            }
        );
    }

    public function supports(Operation $operation, mixed $value): bool
    {
        return $this->getAccessor($operation, $value) !== null;
    }

    public function get(Path $path, mixed $data, string ...$flags): mixed
    {
        $context = $this->createContext(Operation::Get, ...$flags);

        return $this->getValue($path, $data, $context);
    }

    /**
     * @throws InvalidPathException
     * @throws PropertyNotFoundException
     * @throws InvalidInputException
     */
    public function set(Path $path, mixed &$data, mixed $value, string ...$flags): void
    {
        if($path->isEmpty()) {
            throw InvalidPathException::emptyPath();
        }

        $context = $this->createContext(Operation::Set, ...$flags);

        $this->setValue($path, $data, $value, $context);
    }

    /**
     * @throws PropertyNotFoundException
     * @throws InvalidPathException
     * @throws OperationNotSupportedException
     * @throws InvalidInputException
     */
    public function push(Path $path, mixed &$data, mixed $value, string ...$flags): void
    {
        $context = $this->createContext(Operation::Push, ...$flags);

        $this->write($path, $data, $value, $context);
    }

    /**
     * @throws PropertyNotFoundException
     * @throws OperationNotSupportedException
     * @throws InvalidPathException
     * @throws InvalidInputException
     */
    public function merge(Path $path, mixed &$data, mixed $value, string ...$flags): void
    {
        $context = $this->createContext(Operation::Merge, ...$flags);

        $this->write($path, $data, $value, $context);
    }

    /**
     * @throws PropertyNotFoundException
     * @throws InvalidPathException
     * @throws InvalidInputException
     */
    public function collect(PathCollection $paths, mixed $data, string ...$flags): array
    {
        $context = $this->createContext(Operation::Collect, ...$flags);

        $result = [];

        /** @var Path $path */
        foreach($paths as $path) {
            $set = $this->collectValues($path, $data, $context);

            $result = \array_merge_recursive($result, $set);
        }

        return $result;
    }

    /**
     * @throws InvalidPathException
     */
    public function has(Path $path, mixed $data, string ...$flags): bool
    {
        if($path->isEmpty()) {
            throw InvalidPathException::emptyPath();
        }

        if($data === null) {
            return false;
        }

        $context = $this->createContext(Operation::Has, ...$flags);

        $pointer = $data;
        $currentPath = [];

        foreach($path as $field) {
            if($pointer === null) {
                return false;
            }

            $currentPath[] = $field;

            if(!$this->access($field, $pointer, null, $context->subContext(Operation::Has, new Path($currentPath)))) {
                return false;
            }

            $pointer = $this->access($field, $pointer, null, $context->subContext(Operation::Get, new Path($currentPath)));
        }

        return true;
    }

    /**
     * @throws PropertyNotFoundException
     * @throws InvalidInputException
     * @throws OperationNotSupportedException
     * @throws InvalidPathException
     */
    public function write(Path $path, mixed &$data, mixed $value, AccessContext $context): void
    {
        if($data === null) {
            throw new InvalidInputException('Value of null is not writable');
        }

        if($path->isEmpty()) {
            $this->access(null, $data, $value, $context);

            return;
        }

        $chain = $this->readChain($path, $data, $context);

        $pointer = &$chain[\count($chain) - 1]['value'];
        $this->access(null, $pointer, $value, $context);

        $this->writeChain($chain, $data, $context);
    }

    public function createContext(Operation $operation, string ...$flags): AccessContext
    {
        return new AccessContext($operation, new Path(), $this, ...$flags);
    }

    private function getValue(Path $path, mixed $data, AccessContext $context): mixed
    {
        if($data === null) {
            return null;
        }

        $pointer = $data;
        $currentPath = [];

        foreach ($path as $index => $field) {
            $currentPath[] = $field;

            $pointer = $this->access($field, $pointer, null, $context->subContext(Operation::Get, new Path($currentPath)));

            if ($pointer === null) {
                if (($index < ($path->getLength() - 1)) && $context->hasFlags(Flags::STRICT)) {
                    throw new NotAccessibleException(new Path($currentPath), \get_debug_type($pointer), Operation::Get);
                }

                return null;
            }
        }

        return $pointer;
    }

    /**
     * @throws InvalidPathException
     * @throws InvalidInputException
     * @throws PropertyNotFoundException
     */
    private function setValue(Path $path, mixed &$data, mixed $value, AccessContext $context): void
    {
        if($data === null) {
            throw new InvalidInputException('Cannot set value to a null.');
        }

        if($path->isEmpty()) {
            throw InvalidPathException::emptyPath();
        }

        $chain = $this->readChain($path, $data, $context);
        $chain[\count($chain) - 1]['value'] = $value;

        $this->writeChain($chain, $data, $context);
    }

    /**
     * @throws PropertyNotFoundException
     * @throws InvalidPathException
     * @throws InvalidInputException
     * @throws NotAccessableException
     */
    private function collectValues(Path $path, mixed $data, AccessContext $context): array
    {
        if($data === null) {
            throw new InvalidInputException('Cannot collect from a null value.');
        }

        $result = [];

        if(!Util::hasCollector($path)) {
            $subContext = $context->subContext(Operation::Get, new Path());
            $subContext->addFlag(Flags::STRICT);

            $resultPath = $context->getPath()->merge($path);
            try {
                $value = $this->getValue($path, $data, $subContext);

                if($value !== null) {
                    $result[(string) $resultPath] = $value;
                }
            } catch(NotAccessableException|PropertyNotFoundException $e) {
                if($context->hasFlags(Flags::STRICT)) {
                    throw $e;
                }

                return [];
            }

            return $context->hasFlags(Flags::COLLECT_NESTED) ? Util::flatToNested($result) : $result;
        }

        $pointer = $data;
        $currentPath = new Path();
        $path = $path->toArray();

        /** @var string $field */
        while(($field = \array_shift($path)) !== null) {
            if($field !== Util::COLLECTOR_FIELD) {
                $currentPath->add($field);
                $pointer = $this->access($field, $pointer, null, $context->subContext(Operation::Get, $currentPath));

                if($pointer === null) {
                    return $result;
                }

                continue;
            }

            try {
                $pointer = $this->access(null, $pointer, null, $context->subContext(Operation::Collect, $currentPath));
            } catch(NotAccessableException $e) {
                if($context->hasFlags(Flags::STRICT)) {
                    throw $e;
                }

                return [];
            }

            if(!\is_iterable($pointer)) {
                if($context->hasFlags(Flags::STRICT) && $pointer !== null) {
                    throw new NotAccessableException($currentPath->copy(), \get_debug_type($pointer), Operation::Collect);
                }

                return [];
            }

            foreach($pointer as $index => $item) {
                if(empty($path)) {
                    $itemPath = $context->getPath()->merge($currentPath)->add((string) $index);
                    $result[(string) $itemPath] = $item;

                    continue;
                }

                $subContext = $context->subContext(Operation::Collect, $currentPath->copy()->add((string) $index));
                $subContext->removeFlag(Flags::COLLECT_NESTED);

                $itemResult = $this->collectValues(new Path($path), $item, $subContext);

                $result = \array_merge($result, $itemResult);
            }

            return $context->hasFlags(Flags::COLLECT_NESTED) ? Util::flatToNested($result) : $result;
        }

        return $context->hasFlags(Flags::COLLECT_NESTED) ? Util::flatToNested($result) : $result;
    }

    /**
     * @throws PropertyNotFoundException
     * @throws InvalidPathException
     * @throws OperationNotSupportedException
     * @throws InvalidInputException
     */
    private function access(string|null $field, mixed &$data, mixed $value, AccessContext $context): mixed
    {
        $accessor = $this->getAccessor($context->getOperation(), $data);

        if($accessor === null) {
            if(!$context->hasFlags(Flags::STRICT)) {
                return null;
            }

            $path = $context->getPath()->copy();
            $path->pop();

            throw new NotAccessibleException($path, \get_debug_type($data), $context->getOperation());
        }

        return $accessor->access($field, $data, $value, $context);
    }

    private function getAccessor(Operation $operation, mixed $value): ?Accessor
    {
        foreach($this->accessors as $record) {
            $accessor = $record['accessor'];

            if($accessor->supports($operation, $value)) {
                return $accessor;
            }
        }

        return null;
    }

    /**
     * @throws PropertyNotFoundException
     */
    private function readChain(Path $path, mixed $data, AccessContext $context): array
    {
        $chain = [];
        $currentPath = [];
        $pointer = $data;

        foreach($path as $index => $field) {
            $currentPath[] = $field;
            $subContext = $context->subContext(Operation::Get, new Path($currentPath));
            $subContext->addFlag(Flags::STRICT);

            try {
                $pointer = $this->access($field, $pointer, null, $subContext);
            } catch (PropertyNotFoundException $e) {
                if ($context->hasFlags(Flags::STRICT)) {
                    throw $e;
                }

                $pointer = null;
            }

            $chain[] = [
                'field' => $field,
                'value' => $pointer,
            ];
        }

        return $chain;
    }

    private function writeChain(array $chain, mixed &$data, AccessContext $context): void
    {
        $currentElement = \array_pop($chain);
        $currentPath = [];

        foreach(\array_reverse($chain) as $record) {
            $currentValue = $record['value'];
            $field = $currentElement['field'];

            $this->access($field, $currentValue, $currentElement['value'], $context->subContext(Operation::Set, $this->createPathFromReverse($context->getPath(), new Path($currentPath))));

            if (\is_object($currentValue)) {
                return;
            }

            $currentPath[] = $field;
            $currentElement = [
                'field' => $record['field'],
                'value' => $currentValue,
            ];
        }

        $this->access($currentElement['field'], $data, $currentElement['value'], $context->subContext(Operation::Set, $this->createPathFromReverse($context->getPath(), new Path($currentPath))));
    }

    private function createPathFromReverse(Path $path, Path $subPath): Path
    {
        $path = $path->copy();
        $subPath = new Path(\array_reverse($subPath->toArray()));
        $result = new Path();

        while(($field = $path->shift()) !== null) {
            $result->add($field);

            if($path->equals($subPath)) {
                return $result;
            }
        }

        return $result;
    }
}
