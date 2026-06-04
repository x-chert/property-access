<?php

namespace Xchert\PropertyAccess\Exception;

use Xchert\PropertyAccess\Operation;
use Xchert\PropertyAccess\Path;

class NotAccessableException extends \RuntimeException
{
    public function __construct(
        private readonly Path $path,
        private readonly string $type,
        private readonly Operation $operation
    ) {
        $message = \sprintf('Value of type %s ', $type);

        if(!$path->isEmpty()) {
            $message .= \sprintf("at '%s' ", $path);
        }

        $message .= \sprintf("is not accessable for operation '%s'.", $this->operation->value);

        parent::__construct($message);
    }

    public function getOperation(): Operation
    {
        return $this->operation;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getProperty(): ?string
    {
        $path = $this->path->toArray();
        return \array_pop($path);
    }

    public function getPath(): Path
    {
        return $this->path;
    }
}
