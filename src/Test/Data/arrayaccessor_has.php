<?php

declare(strict_types=1);

use Xchert\Util\Exception\InvalidTypeException;

return (function (): Generator {
    yield from [
        'ValidPath' => [
            'data' => ['foo' => 'bar', 'hello' => 'world'],
            'field' => 'foo',
            'expected' => true,
            'expectedException' => null,
            'flags' => []
        ],
        'InvalidPath' => [
            'data' => ['items' => [new stdClass(), new stdClass()]],
            'field' => 'nonexistingproperty',
            'expected' => false,
            'expectedException' => null,
            'flags' => []
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