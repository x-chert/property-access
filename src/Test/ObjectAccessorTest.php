<?php

namespace Xchert\PropertyAccess\Test;

use PHPUnit\Framework\TestCase;
use Xchert\PropertyAccess\AccessContext;
use Xchert\PropertyAccess\Exception\OperationNotSupportedException;
use Xchert\PropertyAccess\Exception\PropertyNotFoundException;
use Xchert\PropertyAccess\Flags;
use Xchert\PropertyAccess\ObjectAccessor;
use Xchert\PropertyAccess\Operation;
use Xchert\PropertyAccess\Path;
use Xchert\PropertyAccess\PropertyAccessor;
use Xchert\PropertyAccess\Test\Classes\ChildDemo;

class ObjectAccessorTest extends TestCase
{
    public function testGetPropertyDirectly(): void
    {
        $object = new class {
            private string $name = 'John';
        };

        $accessor = new ObjectAccessor();
        $propertyAccessor = $this->createPropertyAccessor($accessor);
        $context = $this->createContext([], Operation::Get, $propertyAccessor);

        $result = $accessor->get('name', $object, $context);

        $this->assertEquals('John', $result);
    }

    public function testGetMethodViaGetter(): void
    {
        $object = new class() {
            private string $name = 'John';
            public bool $getNameCalled = false;

            public function getName(): string
            {
                $this->getNameCalled = true;
                return $this->name;
            }
        };

        $accessor = new ObjectAccessor();
        $propertyAccessor = $this->createPropertyAccessor($accessor);
        $context = $this->createContext([], Operation::Get, $propertyAccessor);

        $result = $accessor->get('name', $object, $context);

        $this->assertEquals('John', $result);
        $this->assertTrue($object->getNameCalled, 'getName method was not called');
    }

    public function testGetReturnsNullForUninitializedProperty(): void
    {
        $object = new class {
            public string $name;
        };

        $accessor = new ObjectAccessor();
        $propertyAccessor = $this->createPropertyAccessor($accessor);
        $context = $this->createContext([], Operation::Get, $propertyAccessor);

        $result = $accessor->get('name', $object, $context);

        $this->assertNull($result);
    }

    public function testGetThrowsPropertyNotFoundExceptionWithStrictFlag(): void
    {
        $object = new \stdClass();

        $accessor = new ObjectAccessor();
        $propertyAccessor = $this->createPropertyAccessor($accessor);
        $context = $this->createContext([Flags::STRICT], Operation::Get, $propertyAccessor);

        $this->expectException(PropertyNotFoundException::class);
        $accessor->get('nonexistentProperty', $object, $context);
    }

    public function testGetReturnsNullIfPropertyDoesNotExistWithoutStrictFlag(): void
    {
        $object = new \stdClass();

        $accessor = new ObjectAccessor();
        $propertyAccessor = $this->createPropertyAccessor($accessor);
        $context = $this->createContext([], Operation::Get, $propertyAccessor);

        $result = $accessor->get('nonexistentProperty', $object, $context);

        $this->assertNull($result);
    }

    public function testGetInheritedProperty(): void
    {
        $object = new ChildDemo();

        $accessor = new ObjectAccessor();
        $propertyAccessor = $this->createPropertyAccessor($accessor);
        $context = $this->createContext([], Operation::Get, $propertyAccessor);

        $result = $accessor->get('birthday', $object, $context);

        $this->assertEquals('1990-01-01', $result);
    }

    public function testGetInheritedPropertyViaGetter(): void
    {
        $object = new ChildDemo();

        $accessor = new ObjectAccessor();
        $propertyAccessor = $this->createPropertyAccessor($accessor);
        $context = $this->createContext([], Operation::Get, $propertyAccessor);

        $result = $accessor->get('name', $object, $context);

        $this->assertEquals('John', $result);
        $this->assertTrue($object->isGotName());
    }

    public function testSetPropertyDirectly(): void
    {
        $object = new class {
            private string $name = 'John';

            public function getName(): string
            {
                return $this->name;
            }
        };

        $accessor = new ObjectAccessor();
        $propertyAccessor = $this->createPropertyAccessor($accessor);
        $context = $this->createContext([], Operation::Set, $propertyAccessor);

        $accessor->set('name', $object, 'Doe', $context);

        $this->assertEquals('Doe', $object->getName());
    }

    public function testSetMethodViaSetter(): void
    {
        $object = new class {
            private string $name = 'John';
            public bool $setNameCalled = false;

            public function setName(string $name): void
            {
                $this->setNameCalled = true;
                $this->name = $name;
            }

            public function getName(): string
            {
                return $this->name;
            }
        };

        $accessor = new ObjectAccessor();
        $propertyAccessor = $this->createPropertyAccessor($accessor);
        $context = $this->createContext([], Operation::Set, $propertyAccessor);

        $accessor->set('name', $object, 'Doe', $context);

        $this->assertEquals('Doe', $object->getName());
        $this->assertTrue($object->setNameCalled, 'setName method was not called');
    }

    public function testSetThrowsPropertyNotFoundExceptionWithStrictFlag(): void
    {
        $object = new \stdClass();

        $accessor = new ObjectAccessor();
        $propertyAccessor = $this->createPropertyAccessor($accessor);
        $context = $this->createContext([Flags::STRICT], Operation::Set, $propertyAccessor);

        $this->expectException(PropertyNotFoundException::class);
        $accessor->set('nonexistentProperty', $object, 'value', $context);
    }

    public function testSetDoesNothingIfPropertyDoesNotExistWithoutStrictFlag(): void
    {
        $object = new \stdClass();

        $accessor = new ObjectAccessor();
        $propertyAccessor = $this->createPropertyAccessor($accessor);
        $context = $this->createContext([], Operation::Set, $propertyAccessor);

        $accessor->set('nonexistentProperty', $object, 'value', $context);

        $this->assertTrue(true, 'Setting nonexistent property without strict flag should not throw an exception');
    }

    public function testSetInheritedPropertyDirectly(): void
    {
        $object = new ChildDemo();

        $accessor = new ObjectAccessor();
        $propertyAccessor = $this->createPropertyAccessor($accessor);
        $context = $this->createContext([], Operation::Set, $propertyAccessor);

        $accessor->set('birthday', $object, '2000-12-31', $context);

        $this->assertEquals('2000-12-31', $object->birthday);
    }

    public function testSetInheritedPropertyViaSetter(): void
    {
        $object = new ChildDemo();

        $accessor = new ObjectAccessor();
        $propertyAccessor = $this->createPropertyAccessor($accessor);
        $context = $this->createContext([], Operation::Set, $propertyAccessor);

        $accessor->set('name', $object, 'Jane', $context);

        $this->assertEquals('Jane', $object->getName());
        $this->assertTrue($object->isSetName());
    }

    public function testHasReturnsTrueForExistingProperty(): void
    {
        $object = new class {
            public string $name = 'John';
        };

        $accessor = new ObjectAccessor();
        $propertyAccessor = $this->createPropertyAccessor($accessor);
        $context = $this->createContext([], Operation::Get, $propertyAccessor);

        $result = $accessor->has('name', $object, $context);

        $this->assertTrue($result, 'has method did not return true for an existing property');
    }

    public function testHasReturnsFalseForNonExistentProperty(): void
    {
        $object = new class {
            public string $name = 'John';
        };

        $accessor = new ObjectAccessor();
        $propertyAccessor = $this->createPropertyAccessor($accessor);
        $context = $this->createContext([], Operation::Get, $propertyAccessor);

        $result = $accessor->has('nonexistent', $object, $context);

        $this->assertFalse($result, 'has method did not return false for a nonexistent property');
    }

    public function testHasReturnsTrueForInheritedProperty(): void
    {
        $object = new ChildDemo();

        $accessor = new ObjectAccessor();
        $propertyAccessor = $this->createPropertyAccessor($accessor);
        $context = $this->createContext([], Operation::Has, $propertyAccessor);

        $result = $accessor->has('name', $object, $context);

        $this->assertTrue($result, 'has method did not return true for inherited property');
    }

    public function testPushThrowsOperationNotSupportedException(): void
    {
        $object = new \stdClass();

        $accessor = new ObjectAccessor();
        $propertyAccessor = $this->createPropertyAccessor($accessor);
        $context = $this->createContext([], Operation::Push, $propertyAccessor);

        $this->expectException(OperationNotSupportedException::class);
        $accessor->push($object, 'value', $context);
    }

    public function testCollectThrowsOperationNotSupportedException(): void
    {
        $object = new \stdClass();

        $accessor = new ObjectAccessor();
        $propertyAccessor = $this->createPropertyAccessor($accessor);
        $context = $this->createContext([], Operation::Collect, $propertyAccessor);

        $this->expectException(OperationNotSupportedException::class);
        $accessor->collect($object, $context);
    }

    private function createPropertyAccessor(ObjectAccessor $accessor): PropertyAccessor
    {
        $propertyAccessor = new PropertyAccessor();
        $propertyAccessor->registerAccessor($accessor, ObjectAccessor::ID, 0);

        return $propertyAccessor;
    }

    private function createContext(array $flags, Operation $operation, PropertyAccessor $propertyAccessor): AccessContext
    {
        return new AccessContext($operation, new Path(), $propertyAccessor, ...$flags);
    }
}