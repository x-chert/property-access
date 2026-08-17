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
                $obj->a->b = ['a', 'b', 'c'];
                return $obj;
            })(),
            'path' => new Path(['a', 'b']),
            'value' => 'd',
            'expected' => (function () {
                $obj = new stdClass();
                $obj->a = new stdClass();
                $obj->a->b = ['a', 'b', 'c', 'd'];
                return $obj;
            })(),
            'expectedException' => null,
            'flags' => []
        ],
        'Invalid path' => [
            'data' => (function () {
                $obj = new stdClass();
                $obj->a = new stdClass();
                $obj->a->b = ['a', 'b', 'c'];
                return $obj;
            })(),
            'path' => new Path(['a', 'nonexistingproperty']),
            'value' => 'some value',
            'expected' => (function () {
                $obj = new stdClass();
                $obj->a = new stdClass();
                $obj->a->b = ['a', 'b', 'c'];
                return $obj;
            })(),
            'expectedException' => null,
            'flags' => []
        ],
        'Invalid path - strict mode' => [
            'data' => (function () {
                $obj = new stdClass();
                $obj->a = new stdClass();
                $obj->a->b = ['a', 'b', 'c'];
                return $obj;
            })(),
            'path' => new Path(['a', 'nonexistingproperty']),
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