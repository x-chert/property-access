<?php

declare(strict_types=1);

use Xchert\Util\Exception\InvalidTypeException;

return (function (): Generator {
    yield from [
        'Push' => [
            'data' => ['a', 'b', 'c'],
            'value' => 'd',
            'expected' => ['a', 'b', 'c', 'd'],
            'expectedException' => null,
            'flags' => []
        ],
        'InvalidTypeException' => [
            'data' => 'not an array',
            'value' => null,
            'expected' => null,
            'expectedException' => InvalidTypeException::class,
            'flags' => []
        ]
    ];
})();