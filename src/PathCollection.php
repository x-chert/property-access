<?php

namespace Xchert\PropertyAccess;

use Xchert\Util\Pod\Collection;

class PathCollection extends Collection
{
    public function hasPath(Path $path): bool
    {
        /** @var Path $comparePath */
        foreach($this->items as $comparePath) {
            if($comparePath->equals($path)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param Path $value
     */
    protected function createKey(mixed $value): ?string
    {
        return (string) $value;
    }

    protected function supports(mixed $value): bool
    {
        return $value instanceof Path;
    }
}
