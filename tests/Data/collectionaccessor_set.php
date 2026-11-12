<?php

declare(strict_types=1);

use Xchert\Util\Exception\InvalidTypeException;
use Xchert\Util\Pod\Collection;

return (function (): Generator {
    yield from [
        'Add' => [
            'data' => new Collection(['foo' => 'bar']),
            'field' => 'hello',
            'value' => 'world',
            'expected' => new Collection(['foo' => 'bar', 'hello' => 'world']),
            'expectedException' => null,
            'flags' => []
        ],
        'Overwrite' => [
            'data' => new Collection(['foo' => 'bar']),
            'field' => 'foo',
            'value' => 'another bar',
            'expected' => new Collection(['foo' => 'another bar']),
            'expectedException' => null,
            'flags' => []
        ],
        'OverwriteNumericKey' => [
            'data' => new Collection(['a', 'b', 'c']),
            'field' => '1',
            'value' => 'x',
            'expected' => new Collection(['a', 'x', 'c']),
            'expectedException' => null,
            'flags' => []
        ],
        'InvalidTypeException' => [
            'data' => ['foo' => 'bar'],
            'field' => 'some field',
            'value' => null,
            'expected' => null,
            'expectedException' => InvalidTypeException::class,
            'flags' => []
        ]
    ];
})();
