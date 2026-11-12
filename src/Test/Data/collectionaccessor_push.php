<?php

declare(strict_types=1);

use Xchert\Util\Exception\InvalidTypeException;
use Xchert\Util\Pod\Collection;

return (function (): Generator {
    yield from [
        'Push' => [
            'data' => new Collection(['a', 'b', 'c']),
            'value' => 'd',
            'expected' => new Collection(['a', 'b', 'c', 'd']),
            'expectedException' => null,
            'flags' => []
        ],
        'UnsupportedItem' => [
            'data' => new Collection(['a', 'b', 'c']),
            'value' => null,
            'expected' => null,
            'expectedException' => InvalidArgumentException::class,
            'flags' => []
        ],
        'InvalidTypeException' => [
            'data' => ['a', 'b', 'c'],
            'value' => 'd',
            'expected' => null,
            'expectedException' => InvalidTypeException::class,
            'flags' => []
        ]
    ];
})();
