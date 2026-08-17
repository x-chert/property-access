<?php

declare(strict_types=1);

use Xchert\PropertyAccess\Exception\NotAccessibleException;
use Xchert\PropertyAccess\Exception\PropertyNotFoundException;
use Xchert\PropertyAccess\Flags;
use Xchert\PropertyAccess\Path;
use Xchert\PropertyAccess\PathCollection;
use Xchert\PropertyAccess\Util;

return (function (): Generator {
    yield from [
        'SimpleCollect' => [
            'data' => (function () {
                $obj = new stdClass();
                $obj->a = new stdClass();
                $obj->a->b = ['a', 'b', 'c'];
                return $obj;
            })(),
            'paths' => new PathCollection([new Path(['a', 'b', Util::COLLECTOR_FIELD])]),
            'expected' => [
                '["a","b","0"]' => 'a',
                '["a","b","1"]' => 'b',
                '["a","b","2"]' => 'c',
            ],
            'expectedException' => null,
            'flags' => []
        ],
        'Multicollect' => [
            'data' => (function () {
                $obj = new stdClass();
                $obj->products = ['ABC-123', 'DEF-456'];
                $obj->orders = ['XXX-789', 'YYY-012'];
                return $obj;
            })(),
            'paths' => new PathCollection([
                new Path(['products', Util::COLLECTOR_FIELD]),
                new Path(['orders', Util::COLLECTOR_FIELD]),
            ]),
            'expected' => [
                '["products","0"]' => 'ABC-123',
                '["products","1"]' => 'DEF-456',
                '["orders","0"]' => 'XXX-789',
                '["orders","1"]' => 'YYY-012',
            ],
            'expectedException' => null,
            'flags' => []
        ],
        'DeepMultiCollect' => [
            'data' => (function () {
                $obj = new stdClass();
                $obj->products = [
                    (function () {
                        $a = new stdClass();
                        $a->productNumber = 'ABC-123';
                        return $a;
                    })(),
                    (function () {
                        $a = new stdClass();
                        $a->productNumber = 'DEF-456';
                        return $a;
                    })()
                ];
                $obj->orders = [
                    (function () {
                        $a = new stdClass();
                        $a->orderNumber = 'XXX-789';
                        return $a;
                    })(),
                    (function () {
                        $a = new stdClass();
                        $a->orderNumber = 'YYY-012';
                        return $a;
                    })()
                ];
                return $obj;
            })(),
            'paths' => new PathCollection([
                new Path(['products', Util::COLLECTOR_FIELD, 'productNumber']),
                new Path(['orders', Util::COLLECTOR_FIELD, 'orderNumber']),
            ]),
            'expected' => [
                '["products","0","productNumber"]' => 'ABC-123',
                '["products","1","productNumber"]' => 'DEF-456',
                '["orders","0","orderNumber"]' => 'XXX-789',
                '["orders","1","orderNumber"]' => 'YYY-012',
            ],
            'expectedException' => null,
            'flags' => []
        ],
        'DeepMultiMultipleCollect' => [
            'data' => (function () {
                $obj = new stdClass();
                $obj->products = [
                    (function () {
                        $a = new stdClass();
                        $a->stocks = ['WH-1' => 11, 'WH-2' => 22];
                        return $a;
                    })(),
                    (function () {
                        $a = new stdClass();
                        $a->stocks = ['WH-3' => 33, 'WH-4' => 44];
                        return $a;
                    })()
                ];
                $obj->orders = [
                    (function () {
                        $a = new stdClass();
                        $a->orderNumber = 'XXX-789';
                        return $a;
                    })(),
                    (function () {
                        $a = new stdClass();
                        $a->orderNumber = 'YYY-012';
                        return $a;
                    })()
                ];
                return $obj;
            })(),
            'paths' => new PathCollection([
                new Path(['products', Util::COLLECTOR_FIELD, 'stocks', Util::COLLECTOR_FIELD]),
                new Path(['orders', Util::COLLECTOR_FIELD, 'orderNumber']),
            ]),
            'expected' => [
                '["products","0","stocks","WH-1"]' => 11,
                '["products","0","stocks","WH-2"]' => 22,
                '["products","1","stocks","WH-3"]' => 33,
                '["products","1","stocks","WH-4"]' => 44,
                '["orders","0","orderNumber"]' => 'XXX-789',
                '["orders","1","orderNumber"]' => 'YYY-012',
            ],
            'expectedException' => null,
            'flags' => []
        ],
        'NestedDeepMultiMultipleCollect' => [
            'data' => (function () {
                $obj = new stdClass();
                $obj->products = [
                    (function () {
                        $a = new stdClass();
                        $a->stocks = ['WH-1' => 11, 'WH-2' => 22];
                        return $a;
                    })(),
                    (function () {
                        $a = new stdClass();
                        $a->stocks = ['WH-3' => 33, 'WH-4' => 44];
                        return $a;
                    })()
                ];
                $obj->orders = [
                    (function () {
                        $a = new stdClass();
                        $a->orderNumber = 'XXX-789';
                        return $a;
                    })(),
                    (function () {
                        $a = new stdClass();
                        $a->orderNumber = 'YYY-012';
                        return $a;
                    })()
                ];
                return $obj;
            })(),
            'paths' => new PathCollection([
                new Path(['products', Util::COLLECTOR_FIELD, 'stocks', Util::COLLECTOR_FIELD]),
                new Path(['orders', Util::COLLECTOR_FIELD, 'orderNumber']),
            ]),
            'expected' => [
                'products' => [
                    0 => [
                        'stocks' => [
                            'WH-1' => 11,
                            'WH-2' => 22,
                        ]
                    ],
                    1 => [
                        'stocks' => [
                            'WH-3' => 33,
                            'WH-4' => 44,
                        ]
                    ]
                ],
                'orders' => [
                    0 => [
                        'orderNumber' => 'XXX-789'
                    ],
                    1 => [
                        'orderNumber' => 'YYY-012'
                    ]
                ]
            ],
            'expectedException' => null,
            'flags' => [Flags::COLLECT_NESTED]
        ],
        'InvalidSimpleCollect' => [
            'data' => ['items' => [new stdClass(), new stdClass()]],
            'paths' => new PathCollection([new Path(['items', '0', 'nonexistingproperty'])]),
            'expected' => [],
            'expectedException' => null,
            'flags' => []
        ],
        'StrictInvalidSimpleCollect' => [
            'data' => ['items' => [new stdClass(), new stdClass()]],
            'paths' => new PathCollection([new Path(['items', '0', 'nonexistingproperty'])]),
            'expected' => null,
            'expectedException' => PropertyNotFoundException::class,
            'flags' => [Flags::STRICT]
        ],
        'NotAccessibleExceptionOnNull' => [
            'data' => ['key' => null],
            'paths' => new PathCollection([new Path(['key', 'property'])]),
            'expected' => null,
            'expectedException' => NotAccessibleException::class,
            'flags' => [Flags::STRICT]
        ],
        'NotAccessibleExceptionOnScalar' => [
            'data' => (function () {
                $obj = new stdClass();
                $obj->level1 = 42;
                return $obj;
            })(),
            'paths' => new PathCollection([new Path(['level1', 'level2', 'level3'])]),
            'expected' => null,
            'expectedException' => NotAccessibleException::class,
            'flags' => [Flags::STRICT]
        ],
    ];
})();