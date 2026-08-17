<?php

declare(strict_types=1);

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
            'expected' => true,
            'expectedException' => null,
            'flags' => []
        ],
        'InheritedValidPath' => [
            'data' => new ChildDemo(),
            'field' => 'name',
            'expected' => true,
            'expectedException' => null,
            'flags' => []
        ],
        'InvalidPath' => [
            'data' => new stdClass(),
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