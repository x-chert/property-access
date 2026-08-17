<?php

declare(strict_types=1);

use Xchert\PropertyAccess\Exception\InvalidPathException;
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
            'expected' => true,
            'expectedException' => null,
            'flags' => []
        ],
        'Invalid path' => [
            'data' => ['items' => [new stdClass(), new stdClass()]],
            'path' => new Path(['items', '0', 'nonexistingproperty']),
            'expected' => false,
            'expectedException' => null,
            'flags' => []
        ],
        'Empty path' => [
            'data' => new stdClass(),
            'path' => new Path([]),
            'expected' => null,
            'expectedException' => InvalidPathException::class,
            'flags' => []
        ]
    ];
})();