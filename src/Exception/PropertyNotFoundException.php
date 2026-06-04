<?php

namespace Xchert\PropertyAccess\Exception;

use Xchert\PropertyAccess\Path;

class PropertyNotFoundException extends \Exception
{
    public function __construct(private readonly Path $path)
    {
        parent::__construct(\sprintf('Property %s was not found.', $path));
    }

    public function getPath(): Path
    {
        return $this->path;
    }
}
