<?php

declare(strict_types=1);

use Xchert\PropertyAccess\ArrayAccessor;
use Xchert\PropertyAccess\Exception\InvalidInputException;
use Xchert\Util\Exception\InvalidTypeException;
use Xchert\Util\Pod\Collection;

return (function (): Generator {
    yield from [
        'Simple merge' => [
            'data' => new Collection(['a' => 'first letter', 'b' => 'second letter']),
            'value' => ['c' => 'third letter', 'd' => 'fourth letter'],
            'expected' => new Collection(['a' => 'first letter', 'b' => 'second letter', 'c' => 'third letter', 'd' => 'fourth letter']),
            'expectedException' => null,
            'flags' => []
        ],
        'NumericMerge' => [
            'data' => new Collection(['a', 'b', 'c']),
            'value' => ['d', 'e', 'f'],
            'expected' => new Collection(['a', 'b', 'c', 'd', 'e', 'f']),
            'expectedException' => null,
            'flags' => []
        ],
        'OverwriteNumericMerge' => [
            'data' => new Collection(['a', 'b', 'c']),
            'value' => ['d', 'e', 'f'],
            'expected' => new Collection(['d', 'e', 'f']),
            'expectedException' => null,
            'flags' => [ArrayAccessor::MERGE_OVERWRITE_NUMERIC]
        ],
        'MergeCollection' => [
            'data' => new Collection(['a' => 'first letter']),
            'value' => new Collection(['b' => 'second letter']),
            'expected' => new Collection(['a' => 'first letter', 'b' => 'second letter']),
            'expectedException' => null,
            'flags' => []
        ],
        'DeepMerge' => [
            'data' => new Collection([
                'letters' => ['a', 'b'],
                'numbers' => [1, 2]
            ]),
            'value' => [
                'letters' => ['c'],
                'numbers' => [3]
            ],
            'expected' => new Collection([
                'letters' => ['a', 'b', 'c'],
                'numbers' => [1, 2, 3]
            ]),
            'expectedException' => null,
            'flags' => []
        ],
        'InvalidInputException' => [
            'data' => new Collection(['a', 'b', 'c']),
            'value' => 'not mergeable',
            'expected' => null,
            'expectedException' => InvalidInputException::class,
            'flags' => []
        ],
        'InvalidTypeException' => [
            'data' => ['a', 'b', 'c'],
            'value' => ['d'],
            'expected' => null,
            'expectedException' => InvalidTypeException::class,
            'flags' => []
        ]
    ];
})();
