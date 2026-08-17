<?php

namespace Xchert\PropertyAccess\Test;

use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\TestCase;
use Xchert\PropertyAccess\AccessContext;
use Xchert\PropertyAccess\Exception\OperationNotSupportedException;
use Xchert\PropertyAccess\ObjectAccessor;
use Xchert\PropertyAccess\Operation;
use Xchert\PropertyAccess\Path;
use Xchert\PropertyAccess\PropertyAccessor;
use Xchert\PropertyAccess\Test\Data\FileDataProvider;

class ObjectAccessorTest extends TestCase
{
    private ObjectAccessor $accessor;

    #[DataProviderExternal(FileDataProvider::class, 'objectaccessor_get')]
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

    #[DataProviderExternal(FileDataProvider::class, 'objectaccessor_set')]
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

    #[DataProviderExternal(FileDataProvider::class, 'objectaccessor_merge')]
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
    
    #[DataProviderExternal(FileDataProvider::class, 'objectaccessor_has')]
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

    public function testPush(): void
    {
        $this->expectException(OperationNotSupportedException::class);
        $data = [];

        $this->accessor->push($data, 'some value', $this->createContext([], Operation::Push));
    }

    public function testCollect(): void
    {
        $this->expectException(OperationNotSupportedException::class);

        $this->accessor->collect([], $this->createContext([], Operation::Collect));
    }

    protected function setUp(): void
    {
        $this->accessor = new ObjectAccessor();
    }

    private function createContext(array $flags, Operation $operation): AccessContext
    {
        $propertyAccessor = new PropertyAccessor();
        $propertyAccessor->registerAccessor($this->accessor, ObjectAccessor::ID, 0);

        return new AccessContext($operation, new Path(), $propertyAccessor, ...$flags);
    }
}