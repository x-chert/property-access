<?php

declare(strict_types=1);

use Xchert\PropertyAccess\Exception\PropertyNotFoundException;
use Xchert\PropertyAccess\Flags;
use Xchert\Util\Exception\InvalidTypeException;
use Xchert\Util\Pod\Collection;

return (function (): Generator {
    yield from [
        'ValidPath' => [
            'data' => new Collection(['foo' => 'bar', 'hello' => 'world']),
            'field' => 'foo',
            'expected' => 'bar',
            'expectedException' => null,
            'flags' => []
        ],
        'NumericKey' => [
            'data' => new Collection(['a', 'b', 'c']),
            'field' => '1',
            'expected' => 'b',
            'expectedException' => null,
            'flags' => []
        ],
        'InvalidPath' => [
            'data' => new Collection(['foo' => 'bar']),
            'field' => 'nonexistingproperty',
            'expected' => null,
            'expectedException' => null,
            'flags' => []
        ],
        'StrictInvalidPath' => [
            'data' => new Collection(['foo' => 'bar']),
            'field' => 'nonexistingproperty',
            'expected' => null,
            'expectedException' => PropertyNotFoundException::class,
            'flags' => [Flags::STRICT]
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
