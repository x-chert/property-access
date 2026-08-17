# property-access

A flexible PHP library for accessing and manipulating nested data structures (arrays and objects) using a unified path-based interface.

## Installation

```bash
composer require xchert/property-access
```

## Usage

### Registering Accessors

To use the `PropertyAccessor`, you need to register the appropriate accessors for the data types you want to handle.

```php
use Xchert\PropertyAccess\PropertyAccessor;
use Xchert\PropertyAccess\ArrayAccessor;
use Xchert\PropertyAccess\ObjectAccessor;

$propertyAccessor = new PropertyAccessor();

// Register ArrayAccessor for array support
$propertyAccessor->registerAccessor(new ArrayAccessor(), ArrayAccessor::ID, 0);

// Register ObjectAccessor for object support
$propertyAccessor->registerAccessor(new ObjectAccessor(), ObjectAccessor::ID, 0);
```

### Path Basics

Paths are represented by the `Path` class and consist of an array of field names. You can create a path from an array or parse it from a JSON string.

```php
use Xchert\PropertyAccess\Path;

// Using constructor
$path = new Path(['user', 'profile', 'name']);

// Parsing from JSON string
$path = Path::parse('["user", "profile", "name"]');
```

### Methods

#### `has`

Checks if a property exists at the given path.

```php
$data = ['user' => ['profile' => ['name' => 'John']]];
$path = new Path(['user', 'profile', 'name']);

if ($propertyAccessor->has($path, $data)) {
    // Returns true
}
```

#### `get`

Retrieves a value from a path.

```php
$data = ['user' => ['profile' => ['name' => 'John']]];
$path = new Path(['user', 'profile', 'name']);

$name = $propertyAccessor->get($path, $data); // 'John'
```

#### `set`

Sets a value at the given path.

```php
$data = ['user' => ['profile' => ['name' => 'John']]];
$path = new Path(['user', 'profile', 'name']);

$propertyAccessor->set($path, $data, 'Jane');
// $data is now ['user' => ['profile' => ['name' => 'Jane']]]
```

#### `push`

Pushes a value into an array at the given path.

```php
$data = ['tags' => ['php', 'git']];
$path = new Path(['tags']);

$propertyAccessor->push($path, $data, 'composer');
// $data is now ['tags' => ['php', 'git', 'composer']]
```

#### `merge`

Merges a value into the data at the given path.

```php
$data = ['user' => ['roles' => ['admin']]];
$path = new Path(['user']);
$value = ['roles' => ['editor'], 'active' => true];

$propertyAccessor->merge($path, $data, $value);
// $data is now ['user' => ['roles' => ['admin', 'editor'], 'active' => true]]
```

#### `collect`

Collects values from multiple paths, supporting wildcards (`[]`).

```php
use Xchert\PropertyAccess\PathCollection;
use Xchert\PropertyAccess\Util;

$data = [
    'products' => [
        ['id' => 1, 'name' => 'Laptop'],
        ['id' => 2, 'name' => 'Mouse'],
    ]
];

$paths = new PathCollection([
    new Path(['products', Util::COLLECTOR_FIELD, 'name'])
]);

$result = $propertyAccessor->collect($paths, $data);
// $result is:
// [
//     '["products","0","name"]' => 'Laptop',
//     '["products","1","name"]' => 'Mouse',
// ]

// To get a nested array structure, use Flags::COLLECT_NESTED
use Xchert\PropertyAccess\Flags;

$result = $propertyAccessor->collect($paths, $data, Flags::COLLECT_NESTED);
// $result is:
// [
//     'products' => [
//         ['name' => 'Laptop'],
//         ['name' => 'Mouse'],
//     ]
// ]
```

### Strict Mode

By default, the accessor returns `null` or ignores missing properties. You can enable `strict` mode to throw exceptions when a property is not found.

```php
use Xchert\PropertyAccess\Flags;

$propertyAccessor->get($path, $data, Flags::STRICT);
```
