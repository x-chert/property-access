<?php

declare(strict_types=1);

use Xchert\PropertyAccess\Exception\InvalidInputException;
use Xchert\Util\Exception\InvalidTypeException;

return (function (): Generator {
    yield from [
        'SimpleMerge' => [
            'data' => (function () {
                $obj = new stdClass();
                $obj->a = 'first letter';
                $obj->b = 'second letter';
                $obj->c = null;
                $obj->d = null;
                return $obj;
            })(),
            'value' => (function () {
                $obj = new stdClass();
                $obj->b = 'b is indeed the second letter';
                $obj->c = 'third letter';
                $obj->d = 'fourth letter';

                return $obj;
            })(),
            'expected' => (function () {
                $obj = new stdClass();
                $obj->a = 'first letter';
                $obj->b = 'b is indeed the second letter';
                $obj->c = 'third letter';
                $obj->d = 'fourth letter';
                return $obj;
            })(),
            'expectedException' => null,
            'flags' => []
        ],
        'DeepMerge' => [
            'data' => (function () {
                $obj = new stdClass();
                $obj->letters = new stdClass();
                $obj->letters->a = 'a';
                $obj->letters->b = null;

                $obj->numbers = new stdClass();
                $obj->numbers->one = 1;
                $obj->numbers->two = 2;
                $obj->numbers->three = null;

                $obj->scalar = 'a scalar value';
                return $obj;
            })(),
            'value' => (function () {
                $obj = new stdClass();
                $obj->letters = new stdClass();
                $obj->letters->b = 'b';

                $obj->numbers = new stdClass();
                $obj->numbers->two = 22;
                $obj->numbers->three = 33;

                $obj->scalar = 'another scalar value';
                return $obj;
            })(),
            'expected' => (function () {
                $obj = new stdClass();
                $obj->letters = new stdClass();
                $obj->letters->a = 'a';
                $obj->letters->b = 'b';

                $obj->numbers = new stdClass();
                $obj->numbers->one = 1;
                $obj->numbers->two = 22;
                $obj->numbers->three = 33;

                $obj->scalar = 'another scalar value';
                return $obj;
            })(),
            'expectedException' => null,
            'flags' => []
        ],
        'InvalidInputException' => [
            'data' => new stdClass(),
            'value' => 'not mergeable',
            'expected' => null,
            'expectedException' => InvalidInputException::class,
            'flags' => []
        ],
        'InvalidTypeException' => [
            'data' => 'not an object',
            'value' => null,
            'expected' => null,
            'expectedException' => InvalidTypeException::class,
            'flags' => []
        ]
    ];
})();