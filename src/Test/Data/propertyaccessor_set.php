<?php

declare(strict_types=1);

use Xchert\PropertyAccess\Exception\NotAccessibleException;
use Xchert\PropertyAccess\Exception\PropertyNotFoundException;
use Xchert\PropertyAccess\Flags;
use Xchert\PropertyAccess\Path;

return (function (): Generator {
    yield from [
        'Valid path' => [
            'data' => (function () {
                $obj = new stdClass();
                $obj->a = new stdClass();
                $obj->a->b = ['c' => new stdClass()];
                $obj->a->b['c']->d = ['e' => 'very deep'];
                return $obj;
            })(),
            'path' => new Path(['a', 'b', 'c', 'd', 'e']),
            'value' => 'updated very deep',
            'expected' => (function () {
                $obj = new stdClass();
                $obj->a = new stdClass();
                $cObj = new stdClass();
                $cObj->d = ['e' => 'updated very deep'];
                $obj->a->b = ['c' => $cObj];
                return $obj;
            })(),
            'expectedException' => null,
            'flags' => []
        ],
        'Invalid path' => [
            'data' => ['items' => [new stdClass(), new stdClass()]],
            'path' => new Path(['items', '0', 'nonexistingproperty']),
            'value' => 'some value',
            'expected' => ['items' => [new stdClass(), new stdClass()]],
            'expectedException' => null,
            'flags' => []
        ],
        'Invalid path - strict mode' => [
            'data' => ['items' => [new stdClass(), new stdClass()]],
            'path' => new Path(['items', '0', 'nonexistingproperty']),
            'value' => 'some value',
            'expected' => null,
            'expectedException' => PropertyNotFoundException::class,
            'flags' => [Flags::STRICT]
        ],
        'NotAccessibleException - accessing property on null' => [
            'data' => ['key' => null],
            'path' => new Path(['key', 'property']),
            'value' => 'some value',
            'expected' => null,
            'expectedException' => NotAccessibleException::class,
            'flags' => [Flags::STRICT]
        ],
        'NotAccessibleException - traversing through scalar integer' => [
            'data' => (function () {
                $obj = new stdClass();
                $obj->level1 = 42;
                return $obj;
            })(),
            'path' => new Path(['level1', 'level2', 'level3']),
            'value' => 'some value',
            'expected' => null,
            'expectedException' => NotAccessibleException::class,
            'flags' => [Flags::STRICT]
        ],
    ];
})();