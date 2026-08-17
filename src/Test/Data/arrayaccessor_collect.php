<?php

declare(strict_types=1);

use Xchert\Util\Exception\InvalidTypeException;

return (function (): Generator {
    yield from [
        'CollectNumeric' => [
            'data' => [1, 2, 3],
            'expected' => [1, 2, 3],
            'expectedException' => null,
            'flags' => []
        ],
        'CollectAssociative' => [
            'data' => ['foo' => 'bar', 'hello' => 'world'],
            'expected' => ['foo' => 'bar', 'hello' => 'world'],
            'expectedException' => null,
            'flags' => []
        ],
        'InvalidTypeException' => [
            'data' => 'not an array',
            'expected' => null,
            'expectedException' => InvalidTypeException::class,
            'flags' => []
        ]
    ];
})();