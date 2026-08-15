<?php

namespace Xchert\PropertyAccess\Test;

use PHPUnit\Framework\TestCase;
use Xchert\PropertyAccess\AccessContext;
use Xchert\PropertyAccess\ArrayAccessor;
use Xchert\PropertyAccess\Exception\PropertyNotFoundException;
use Xchert\PropertyAccess\Flags;
use Xchert\PropertyAccess\Operation;
use Xchert\PropertyAccess\Path;
use Xchert\PropertyAccess\PropertyAccessor;
use Xchert\Util\Exception\InvalidTypeException;

class ArrayAccessorTest extends TestCase
{
    public function testGet(): void
    {
        $arrayAccessor = new ArrayAccessor();
        $propertyAccessor = $this->createPropertyAccessor($arrayAccessor);
        $data = ['key1' => 'value1', 'key2' => 'value2'];
        $context = $this->createContext([], Operation::Get, $propertyAccessor);

        $result = $arrayAccessor->get('key1', $data, $context);
        $this->assertSame('value1', $result);
    }

    public function testGetNonExistentKey(): void
    {
        $arrayAccessor = new ArrayAccessor();
        $propertyAccessor = $this->createPropertyAccessor($arrayAccessor);
        $data = ['key1' => 'value1'];
        $context = $this->createContext([], Operation::Get, $propertyAccessor);

        $result = $arrayAccessor->get('nonExistentKey', $data, $context);
        $this->assertNull($result);
    }

    public function testGetInvalidTypeThrowsException(): void
    {
        $arrayAccessor = new ArrayAccessor();
        $propertyAccessor = $this->createPropertyAccessor($arrayAccessor);
        $context = $this->createContext([], Operation::Get, $propertyAccessor);

        $this->expectException(InvalidTypeException::class);
        $data = 'notAnArray';
        $arrayAccessor->get('key', $data, $context);
    }

    public function testGetWithStrictFlagThrowsException(): void
    {
        $arrayAccessor = new ArrayAccessor();
        $propertyAccessor = $this->createPropertyAccessor($arrayAccessor);
        $context = $this->createContext([Flags::STRICT], Operation::Get, $propertyAccessor);

        $data = ['key1' => 'value1'];
        $this->expectException(PropertyNotFoundException::class);
        $arrayAccessor->get('nonExistentKey', $data, $context);
    }

    public function testSet(): void
    {
        $arrayAccessor = new ArrayAccessor();
        $propertyAccessor = $this->createPropertyAccessor($arrayAccessor);
        $context = $this->createContext([], Operation::Set, $propertyAccessor);

        $data = [];
        $arrayAccessor->set('key1', $data, 'value1', $context);
        $this->assertSame(['key1' => 'value1'], $data);
    }

    public function testSetOverwrite(): void
    {
        $arrayAccessor = new ArrayAccessor();
        $propertyAccessor = $this->createPropertyAccessor($arrayAccessor);
        $context = $this->createContext([], Operation::Set, $propertyAccessor);

        $data = ['key1' => 'value1'];
        $arrayAccessor->set('key1', $data, 'newValue', $context);
        $this->assertSame(['key1' => 'newValue'], $data);
    }

    public function testSetInvalidTypeThrowsException(): void
    {
        $arrayAccessor = new ArrayAccessor();
        $propertyAccessor = $this->createPropertyAccessor($arrayAccessor);
        $context = $this->createContext([], Operation::Set, $propertyAccessor);

        $this->expectException(InvalidTypeException::class);
        $data = 'notAnArray';
        $arrayAccessor->set('key2', $data, 'value2', $context);
    }

    public function testPushToEmptyArray(): void
    {
        $arrayAccessor = new ArrayAccessor();
        $propertyAccessor = $this->createPropertyAccessor($arrayAccessor);
        $context = $this->createContext([], Operation::Push, $propertyAccessor);

        $data = [];
        $arrayAccessor->push($data, 'value1', $context);
        $this->assertSame(['value1'], $data);
    }

    public function testPushMultiple(): void
    {
        $arrayAccessor = new ArrayAccessor();
        $propertyAccessor = $this->createPropertyAccessor($arrayAccessor);
        $context = $this->createContext([], Operation::Push, $propertyAccessor);

        $data = ['value1'];
        $arrayAccessor->push($data, 'value2', $context);
        $arrayAccessor->push($data, 'value3', $context);
        $this->assertSame(['value1', 'value2', 'value3'], $data);
    }

    public function testPushInvalidTypeThrowsException(): void
    {
        $arrayAccessor = new ArrayAccessor();
        $propertyAccessor = $this->createPropertyAccessor($arrayAccessor);
        $context = $this->createContext([], Operation::Push, $propertyAccessor);

        $this->expectException(InvalidTypeException::class);
        $data = 'notAnArray';
        $arrayAccessor->push($data, 'value4', $context);
    }

    public function testCollect(): void
    {
        $arrayAccessor = new ArrayAccessor();
        $propertyAccessor = $this->createPropertyAccessor($arrayAccessor);
        $context = $this->createContext([], Operation::Collect, $propertyAccessor);

        $data = ['key1' => 'value1', 'key2' => 'value2'];
        $result = $arrayAccessor->collect($data, $context);
        $this->assertSame(['key1' => 'value1', 'key2' => 'value2'], $result);
    }

    public function testCollectInvalidTypeThrowsException(): void
    {
        $arrayAccessor = new ArrayAccessor();
        $propertyAccessor = $this->createPropertyAccessor($arrayAccessor);
        $context = $this->createContext([], Operation::Collect, $propertyAccessor);

        $this->expectException(InvalidTypeException::class);
        $data = 'notAnArray';
        $arrayAccessor->collect($data, $context);
    }

    public function testHas(): void
    {
        $arrayAccessor = new ArrayAccessor();
        $propertyAccessor = $this->createPropertyAccessor($arrayAccessor);
        $context = $this->createContext([], Operation::Has, $propertyAccessor);

        $data = ['existingKey' => 'value'];
        $result = $arrayAccessor->has('existingKey', $data, $context);
        $this->assertTrue($result);
    }

    public function testHasNonExistentKey(): void
    {
        $arrayAccessor = new ArrayAccessor();
        $propertyAccessor = $this->createPropertyAccessor($arrayAccessor);
        $context = $this->createContext([], Operation::Has, $propertyAccessor);

        $data = ['existingKey' => 'value'];
        $result = $arrayAccessor->has('nonExistentKey', $data, $context);
        $this->assertFalse($result);
    }

    public function testHasInvalidTypeThrowsException(): void
    {
        $arrayAccessor = new ArrayAccessor();
        $propertyAccessor = $this->createPropertyAccessor($arrayAccessor);
        $context = $this->createContext([], Operation::Has, $propertyAccessor);

        $this->expectException(InvalidTypeException::class);
        $data = 'notAnArray';
        $arrayAccessor->has('key', $data, $context);
    }

    public function testMerge(): void
    {
        $arrayAccessor = new ArrayAccessor();
        $propertyAccessor = $this->createPropertyAccessor($arrayAccessor);
        $context = $this->createContext([], Operation::Merge, $propertyAccessor);

        $data = ['key1' => 'value1'];
        $arrayAccessor->merge($data, ['key2' => 'value2', 'key3' => 'value3'], $context);
        $this->assertSame(
            ['key1' => 'value1', 'key2' => 'value2', 'key3' => 'value3'],
            $data
        );
    }

    public function testMergeNumericKeys(): void
    {
        $arrayAccessor = new ArrayAccessor();
        $propertyAccessor = $this->createPropertyAccessor($arrayAccessor);
        $context = $this->createContext([], Operation::Merge, $propertyAccessor);

        $data = [0 => 'value1'];
        $arrayAccessor->merge($data, [0 => 'value2'], $context);
        $this->assertSame(
            [0 => 'value1', 1 => 'value2'],
            $data
        );
    }

    public function testMergeOverwriteNumericKeys(): void
    {
        $arrayAccessor = new ArrayAccessor();
        $propertyAccessor = $this->createPropertyAccessor($arrayAccessor);
        $context = $this->createContext([ArrayAccessor::MERGE_OVERWRITE_NUMERIC], Operation::Merge, $propertyAccessor);

        $data = [0 => 'value1'];
        $arrayAccessor->merge($data, [0 => 'value2'], $context);
        $this->assertSame(
            [0 => 'value2'],
            $data
        );
    }

    private function createPropertyAccessor(ArrayAccessor $accessor): PropertyAccessor
    {
        $propertyAccessor = new PropertyAccessor();
        $propertyAccessor->registerAccessor($accessor, ArrayAccessor::ID, 0);

        return $propertyAccessor;
    }

    private function createContext(array $flags, Operation $operation, PropertyAccessor $propertyAccessor): AccessContext
    {
        return new AccessContext($operation, new Path(), $propertyAccessor, ...$flags);
    }
}