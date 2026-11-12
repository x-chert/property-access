<?php

namespace Xchert\PropertyAccess\Test;

use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\TestCase;
use Xchert\PropertyAccess\AccessContext;
use Xchert\PropertyAccess\ArrayAccessor;
use Xchert\PropertyAccess\Operation;
use Xchert\PropertyAccess\Path;
use Xchert\PropertyAccess\PropertyAccessor;
use Xchert\PropertyAccess\Test\Data\FileDataProvider;

class ArrayAccessorTest extends TestCase
{
    private ArrayAccessor $accessor;

    #[DataProviderExternal(FileDataProvider::class, 'arrayaccessor_get')]
    public function testGet(mixed $data, string $field, mixed $expected, ?string $expectedException = null, array $flags = []): void
    {
        $context = $this->createContext($flags, Operation::Get);

        if ($expectedException !== null) {
            $this->expectException($expectedException);
        }

        $result = $this->accessor->get($field, $data, $context);

        if ($expectedException === null) {
            $this->assertEquals($expected, $result);
        }
    }

    #[DataProviderExternal(FileDataProvider::class, 'arrayaccessor_set')]
    public function testSet(mixed $data, string $field, mixed $value, mixed $expected, ?string $expectedException = null, array $flags = []): void
    {
        $context = $this->createContext($flags, Operation::Set);

        if ($expectedException !== null) {
            $this->expectException($expectedException);
        }

        $this->accessor->set($field, $data, $value, $context);

        if ($expectedException === null) {
            $this->assertEquals($expected, $data);
        }
    }

    #[DataProviderExternal(FileDataProvider::class, 'arrayaccessor_push')]
    public function testPush(mixed $data, mixed $value, mixed $expected, ?string $expectedException = null, array $flags = []): void
    {
        $context = $this->createContext($flags, Operation::Push);

        if ($expectedException !== null) {
            $this->expectException($expectedException);
        }

        $this->accessor->push($data, $value, $context);

        if ($expectedException === null) {
            $this->assertEquals($expected, $data);
        }
    }

    #[DataProviderExternal(FileDataProvider::class, 'arrayaccessor_merge')]
    public function testMerge(mixed $data, mixed $value, mixed $expected, ?string $expectedException = null, array $flags = []): void
    {
        $context = $this->createContext($flags, Operation::Merge);

        if ($expectedException !== null) {
            $this->expectException($expectedException);
        }

        $this->accessor->merge($data, $value, $context);

        if ($expectedException === null) {
            $this->assertEquals($expected, $data);
        }
    }

    #[DataProviderExternal(FileDataProvider::class, 'arrayaccessor_collect')]
    public function testCollect(mixed $data, mixed $expected, ?string $expectedException = null, array $flags = []): void
    {
        $context = $this->createContext($flags, Operation::Collect);

        if ($expectedException !== null) {
            $this->expectException($expectedException);
        }

        $result = $this->accessor->collect($data, $context);

        if ($expectedException === null) {
            $this->assertEquals($expected, $result);
        }
    }

    #[DataProviderExternal(FileDataProvider::class, 'arrayaccessor_has')]
    public function testHas(mixed $data, string $field, ?bool $expected, ?string $expectedException = null, array $flags = []): void
    {
        $context = $this->createContext($flags, Operation::Has);

        if ($expectedException !== null) {
            $this->expectException($expectedException);
        }

        $result = $this->accessor->has($field, $data, $context);

        if ($expectedException === null) {
            $this->assertSame($expected, $result);
        }
    }

    protected function setUp(): void
    {
        $this->accessor = new ArrayAccessor();
    }

    private function createContext(array $flags, Operation $operation): AccessContext
    {
        $propertyAccessor = new PropertyAccessor();
        $propertyAccessor->registerAccessor($this->accessor, ArrayAccessor::ID, 0);

        return new AccessContext($operation, new Path(), $propertyAccessor, ...$flags);
    }
}