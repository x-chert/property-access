<?php

declare(strict_types=1);

use Xchert\Util\Exception\InvalidTypeException;

return (function (): Generator {
    yield from [
        'Add' => [
            'data' => ['foo' => 'bar'],
            'field' => 'hello',
            'value' => 'world',
            'expected' => ['foo' => 'bar', 'hello' => 'world'],
            'expectedException' => null,
            'flags' => []
        ],
        'Overwrite' => [
            'data' => ['foo' => 'bar'],
            'field' => 'foo',
            'value' => 'another bar',
            'expected' => ['foo' => 'another bar'],
            'expectedException' => null,
            'flags' => []
        ],
        'InvalidTypeException' => [
            'data' => 'not an array',
            'field' => 'some field',
            'value' => null,
            'expected' => null,
            'expectedException' => InvalidTypeException::class,
            'flags' => []
        ]
    ];
})();