<?php

declare(strict_types=1);

use Xchert\Util\Exception\InvalidTypeException;
use Xchert\Util\Pod\Collection;

return (function (): Generator {
    yield from [
        'Collect' => [
            'data' => new Collection(['a', 'foo' => 'bar', 'c']),
            'expected' => [0 => 'a', 'foo' => 'bar', 1 => 'c'],
            'expectedException' => null,
            'flags' => []
        ],
        'InvalidTypeException' => [
            'data' => ['a', 'b', 'c'],
            'expected' => null,
            'expectedException' => InvalidTypeException::class,
            'flags' => []
        ]
    ];
})();
