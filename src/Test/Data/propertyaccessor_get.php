<?php

declare(strict_types=1);

use Xchert\PropertyAccess\Exception\NotAccessibleException;
use Xchert\PropertyAccess\Exception\PropertyNotFoundException;
use Xchert\PropertyAccess\Flags;
use Xchert\PropertyAccess\Path;

return (function (): Generator {
    yield from [
        'ValidPath' => [
            'data' => (function () {
                $obj = new stdClass();
                $obj->a = new stdClass();
                $obj->a->b = ['c' => new stdClass()];
                $obj->a->b['c']->d = ['e' => 'very deep'];
                return $obj;
            })(),
            'path' => new Path(['a', 'b', 'c', 'd', 'e']),
            'expected' => 'very deep',
            'expectedException' => null,
            'flags' => []
        ],
        'InvalidPath' => [
            'data' => ['items' => [new stdClass(), new stdClass()]],
            'path' => new Path(['items', '0', 'nonexistingproperty']),
            'expected' => null,
            'expectedException' => null,
            'flags' => []
        ],
        'StrictInvalidPath' => [
            'data' => ['items' => [new stdClass(), new stdClass()]],
            'path' => new Path(['items', '0', 'nonexistingproperty']),
            'expected' => null,
            'expectedException' => PropertyNotFoundException::class,
            'flags' => [Flags::STRICT]
        ],
        'NotAccessibleException' => [
            'data' => (function () {
                $obj = new stdClass();
                $obj->level1 = 42;
                return $obj;
            })(),
            'path' => new Path(['level1', 'level2', 'level3']),
            'expected' => null,
            'expectedException' => NotAccessibleException::class,
            'flags' => [Flags::STRICT]
        ],
    ];
})();