<?php

declare(strict_types=1);

namespace Xchert\PropertyAccess;

use Xchert\Util\Trait\FlagTrait;

class AccessContext
{
    use FlagTrait;

    public function __construct(
        private readonly Operation $operation,
        private readonly Path $path,
        private readonly PropertyAccessor $propertyAccessor,
        string ...$flags
    ) {
        $this->setFlags(...$flags);
    }

    public function getPropertyAccessor(): PropertyAccessor
    {
        return $this->propertyAccessor;
    }

    public function getOperation(): Operation
    {
        return $this->operation;
    }

    public function getPath(): Path
    {
        return $this->path;
    }

    public function subContext(Operation $operation, Path $appendPath): self
    {
        return new AccessContext(
            $operation,
            $this->path->merge($appendPath),
            $this->propertyAccessor,
            ...$this->flags
        );
    }
}
