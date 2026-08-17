<?php

declare(strict_types=1);

use Xchert\PropertyAccess\Exception\PropertyNotFoundException;
use Xchert\PropertyAccess\Flags;
use Xchert\PropertyAccess\Test\Classes\ChildDemo;
use Xchert\Util\Exception\InvalidTypeException;

return (function (): Generator {
    yield from [
        'ValidPath' => [
            'data' => (function () {
                $obj = new stdClass();
                $obj->foo = 'bar';
                return $obj;
            })(),
            'field' => 'foo',
            'expected' => 'bar',
            'expectedException' => null,
            'flags' => []
        ],
        'InheritedValidPath' => [
            'data' => new ChildDemo(),
            'field' => 'name',
            'expected' => 'John',
            'expectedException' => null,
            'flags' => []
        ],
        'InvalidPath' => [
            'data' => (function () {
                $obj = new stdClass();
                $obj->foo = 'bar';
                return $obj;
            })(),
            'field' => 'nonexistingproperty',
            'expected' => null,
            'expectedException' => null,
            'flags' => []
        ],
        'StrictInvalidPath' => [
            'data' => (function () {
                $obj = new stdClass();
                $obj->foo = 'bar';
                return $obj;
            })(),
            'field' => 'nonexistingproperty',
            'expected' => null,
            'expectedException' => PropertyNotFoundException::class,
            'flags' => [Flags::STRICT]
        ],
        'InvalidTypeException' => [
            'data' => 'not an object',
            'field' => 'some field',
            'expected' => null,
            'expectedException' => InvalidTypeException::class,
            'flags' => []
        ]
    ];
})();