<?php

namespace Xchert\PropertyAccess\Test;

use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\TestCase;
use Xchert\PropertyAccess\ArrayAccessor;
use Xchert\PropertyAccess\ObjectAccessor;
use Xchert\PropertyAccess\Path;
use Xchert\PropertyAccess\PathCollection;
use Xchert\PropertyAccess\PropertyAccessor;
use Xchert\PropertyAccess\Test\Data\FileDataProvider;

class PropertyAccessorTest extends TestCase
{
    private PropertyAccessor $accessor;

    #[DataProviderExternal(FileDataProvider::class, 'propertyaccessor_get')]
    public function testGet(mixed $data, Path $path, mixed $expected, ?string $expectedException = null, array $flags = []): void
    {
        if ($expectedException !== null) {
            $this->expectException($expectedException);
        }

        $result = $this->accessor->get($path, $data, ...$flags);

        if ($expectedException === null) {
            $this->assertEquals($expected, $result);
        }
    }

    #[DataProviderExternal(FileDataProvider::class, 'propertyaccessor_set')]
    public function testSet(mixed $data, Path $path, mixed $value, mixed $expected, ?string $expectedException = null, array $flags = []): void
    {
        if ($expectedException !== null) {
            $this->expectException($expectedException);
        }

        $this->accessor->set($path, $data, $value, ...$flags);

        if ($expectedException === null) {
            $this->assertEquals($expected, $data);
        }
    }

    #[DataProviderExternal(FileDataProvider::class, 'propertyaccessor_push')]
    public function testPush(mixed $data, Path $path, mixed $value, mixed $expected, ?string $expectedException = null, array $flags = []): void
    {
        if ($expectedException !== null) {
            $this->expectException($expectedException);
        }

        $this->accessor->push($path, $data, $value, ...$flags);

        if ($expectedException === null) {
            $this->assertEquals($expected, $data);
        }
    }

    #[DataProviderExternal(FileDataProvider::class, 'propertyaccessor_merge')]
    public function testMerge(mixed $data, Path $path, mixed $value, mixed $expected, ?string $expectedException = null, array $flags = []): void
    {
        if ($expectedException !== null) {
            $this->expectException($expectedException);
        }

        $this->accessor->merge($path, $data, $value, ...$flags);

        if ($expectedException === null) {
            $this->assertEquals($expected, $data);
        }
    }

    #[DataProviderExternal(FileDataProvider::class, 'propertyaccessor_collect')]
    public function testCollect(mixed $data, PathCollection $paths, mixed $expected, ?string $expectedException = null, array $flags = []): void
    {
        if ($expectedException !== null) {
            $this->expectException($expectedException);
        }

        $result = $this->accessor->collect($paths, $data, ...$flags);

        if ($expectedException === null) {
            $this->assertEquals($expected, $result);
        }
    }

    #[DataProviderExternal(FileDataProvider::class, 'propertyaccessor_has')]
    public function testHas(mixed $data, Path $path, ?bool $expected, ?string $expectedException = null, array $flags = []): void
    {
        if ($expectedException !== null) {
            $this->expectException($expectedException);
        }

        $result = $this->accessor->has($path, $data, ...$flags);

        if ($expectedException === null) {
            $this->assertSame($expected, $result);
        }
    }

    protected function setUp(): void
    {
        $this->accessor = new PropertyAccessor();
        $this->accessor->registerAccessor(new ArrayAccessor(), ArrayAccessor::ID, 0);
        $this->accessor->registerAccessor(new ObjectAccessor(), ObjectAccessor::ID, 0);
    }
}