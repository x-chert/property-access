<?php

declare(strict_types=1);

use Xchert\PropertyAccess\ArrayAccessor;
use Xchert\PropertyAccess\Exception\InvalidInputException;
use Xchert\Util\Exception\InvalidTypeException;

return (function (): Generator {
    yield from [
        'Simple merge' => [
            'data' => ['a' => 'first letter', 'b' => 'second letter'],
            'value' => ['c' => 'third letter', 'd' => 'fourth letter'],
            'expected' => ['a' => 'first letter', 'b' => 'second letter', 'c' => 'third letter', 'd' => 'fourth letter'],
            'expectedException' => null,
            'flags' => []
        ],
        'NumericMerge' => [
            'data' => ['a', 'b', 'c'],
            'value' => ['d', 'e', 'f'],
            'expected' => ['a', 'b', 'c', 'd', 'e', 'f'],
            'expectedException' => null,
            'flags' => []
        ],
        'OverwriteNumericMerge' => [
            'data' => ['a', 'b', 'c'],
            'value' => ['d', 'e', 'f'],
            'expected' => ['d', 'e', 'f'],
            'expectedException' => null,
            'flags' => [ArrayAccessor::MERGE_OVERWRITE_NUMERIC]
        ],
        'DeepMerge' => [
            'data' => [
                'letters' => ['a', 'b', 'c', 'd'],
                'numbers' => [1, 2, 3, 4]
            ],
            'value' => [
                'letters' => ['e', 'f', 'g'],
                'numbers' => [5, 6, 7]
            ],
            'expected' => [
                'letters' => ['a', 'b', 'c', 'd', 'e', 'f', 'g'],
                'numbers' => [1, 2, 3, 4, 5, 6, 7]
            ],
            'expectedException' => null,
            'flags' => []
        ],
        'DeepMergeOverwriteNumeric' => [
            'data' => [
                'letters' => ['a', 'b', 'c', 'd'],
                'stocks' => ['WH-1' => 11, 'WH-2' => 22],
            ],
            'value' => [
                'letters' => ['e', 'f', 'g'],
                'stocks' => ['WH-3' => 33, 'WH-4' => 44],
                'numbers' => [1, 2, 3]
            ],
            'expected' => [
                'letters' => ['e', 'f', 'g', 'd'],
                'stocks' => ['WH-1' => 11, 'WH-2' => 22, 'WH-3' => 33, 'WH-4' => 44],
                'numbers' => [1, 2, 3,]
            ],
            'expectedException' => null,
            'flags' => [ArrayAccessor::MERGE_OVERWRITE_NUMERIC]
        ],
        'InvalidInputException' => [
            'data' => ['a', 'b', 'c'],
            'value' => 'not mergeable',
            'expected' => null,
            'expectedException' => InvalidInputException::class,
            'flags' => []
        ],
        'InvalidTypeException' => [
            'data' => 'not an array',
            'value' => null,
            'expected' => null,
            'expectedException' => InvalidTypeException::class,
            'flags' => []
        ]
    ];
})();