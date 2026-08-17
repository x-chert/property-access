<?php

declare(strict_types=1);

use Xchert\PropertyAccess\Exception\PropertyNotFoundException;
use Xchert\PropertyAccess\Flags;
use Xchert\Util\Exception\InvalidTypeException;

return (function (): Generator {
    yield from [
        'Valid path' => [
            'data' => ['foo' => 'bar', 'hello' => 'world'],
            'field' => 'foo',
            'expected' => 'bar',
            'expectedException' => null,
            'flags' => []
        ],
        'Invalid path' => [
            'data' => ['items' => [new stdClass(), new stdClass()]],
            'field' => 'nonexistingproperty',
            'expected' => null,
            'expectedException' => null,
            'flags' => []
        ],
        'Invalid path - strict mode' => [
            'data' => ['items' => [new stdClass(), new stdClass()]],
            'field' => 'nonexistingproperty',
            'expected' => null,
            'expectedException' => PropertyNotFoundException::class,
            'flags' => [Flags::STRICT]
        ],
        'InvalidTypeException' => [
            'data' => 'not an array',
            'field' => 'some field',
            'expected' => null,
            'expectedException' => InvalidTypeException::class,
            'flags' => []
        ]
    ];
})();