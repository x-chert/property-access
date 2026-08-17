<?php

declare(strict_types=1);

use Xchert\PropertyAccess\Exception\PropertyNotFoundException;
use Xchert\PropertyAccess\Flags;
use Xchert\Util\Exception\InvalidTypeException;

return (function (): Generator {
    yield from [
        'ValidPath' => [
            'data' => ['foo' => 'bar', 'hello' => 'world'],
            'field' => 'foo',
            'expected' => 'bar',
            'expectedException' => null,
            'flags' => []
        ],
        'InvalidPath' => [
            'data' => ['items' => [new stdClass(), new stdClass()]],
            'field' => 'nonexistingproperty',
            'expected' => null,
            'expectedException' => null,
            'flags' => []
        ],
        'StrictInvalidPath' => [
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