<?php

declare(strict_types=1);

use Xchert\Util\Exception\InvalidTypeException;
use Xchert\Util\Pod\Collection;

return (function (): Generator {
    yield from [
        'Has' => [
            'data' => new Collection(['foo' => 'bar']),
            'field' => 'foo',
            'expected' => true,
            'expectedException' => null,
            'flags' => []
        ],
        'HasNumericKey' => [
            'data' => new Collection(['a', 'b']),
            'field' => '1',
            'expected' => true,
            'expectedException' => null,
            'flags' => []
        ],
        'HasNot' => [
            'data' => new Collection(['foo' => 'bar']),
            'field' => 'hello',
            'expected' => false,
            'expectedException' => null,
            'flags' => []
        ],
        'InvalidTypeException' => [
            'data' => ['foo' => 'bar'],
            'field' => 'foo',
            'expected' => null,
            'expectedException' => InvalidTypeException::class,
            'flags' => []
        ]
    ];
})();
