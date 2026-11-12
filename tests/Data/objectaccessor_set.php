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
                $obj->foo = null;
                return $obj;
            })(),
            'field' => 'foo',
            'value' => 'bar',
            'expected' => (function () {
                $obj = new stdClass();
                $obj->foo = 'bar';
                return $obj;
            })(),
            'expectedException' => null,
            'flags' => []
        ],
        'InheritedValidPath' => [
            'data' => new ChildDemo(),
            'field' => 'name',
            'value' => 'Jane',
            'expected' => (function () {
                $obj = new ChildDemo();
                $obj->setName('Jane');
                return $obj;
            })()
        ],
        'InvalidPath' => [
            'data' => new stdClass(),
            'field' => 'nonexistingproperty',
            'value' => 'some value',
            'expected' => new stdClass(),
            'expectedException' => null,
            'flags' => []
        ],
        'StrictInvalidPath' => [
            'data' => new stdClass(),
            'field' => 'nonexistingproperty',
            'value' => 'some value',
            'expected' => null,
            'expectedException' => PropertyNotFoundException::class,
            'flags' => [Flags::STRICT]
        ],
        'InvalidTypeException' => [
            'data' => 'not an object',
            'field' => 'some field',
            'value' => null,
            'expected' => null,
            'expectedException' => InvalidTypeException::class,
            'flags' => []
        ]
    ];
})();