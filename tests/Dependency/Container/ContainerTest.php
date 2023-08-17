<?php

namespace SSF\Test\Dependency\Container;

use PHPUnit\Framework\TestCase;
use SSF\ORM\Dependency\Container;
use SSF\ORM\Dependency\NotFoundException;

class ContainerTest extends TestCase
{
    protected Container $container;

    protected function setUp(): void
    {
        $this->container = new Container();
    }

    public function testThrowsExceptionWhenNotFound()
    {
        try {
            $this->container->get('non-existent-key');
        } catch (\Exception $exception) {
            $this->assertInstanceOf(NotFoundException::class, $exception);
        }
    }

    public function testHasReturnsFalseWhenMissing()
    {
        $this->assertFalse($this->container->has('non-existent-key'));
    }

    public function testHasReturnsTrueWhenPresent()
    {
        $reflector = new \ReflectionClass($this->container);
        $definitions = $reflector->getProperty('definitions');
        $definitions->setValue($this->container, ['existent-key' => null]);

        $this->assertTrue($this->container->has('existent-key'));
    }

    public function testSet()
    {
        $this->container->set(TestDependency::class, [1, 2, 3]);
        $reflector = new \ReflectionProperty($this->container, 'definitions');
        $expected = [TestDependency::class => [1, 2, 3]];

        $this->assertEquals($expected, $reflector->getValue($this->container));
    }

    public function testGetWithNullDefinition()
    {
        $this->container->set(TestDependency::class);
        $object = $this->container->get(TestDependency::class);

        $this->assertInstanceOf(TestDependency::class, $object);
    }

    public function testGetWithNullDefinitionDeep()
    {
        $this->container->set(TestDependency::class);
        $this->container->set(TestService::class);
        $object = $this->container->get(TestService::class);

        $this->assertInstanceOf(TestService::class, $object);
        $this->assertInstanceOf(TestDependency::class, $object->test);
    }

    public function testGetWithAssociativeArrayDefinition()
    {
        $definition = ['a' => 1, 'b' => 2, 'c' => 3];
        $this->container->set(TestDependency::class, $definition);
        $object = $this->container->get(TestDependency::class);

        $this->assertEquals($definition['a'], $object->a);
        $this->assertEquals($definition['b'], $object->b);
        $this->assertEquals($definition['c'], $object->c);
    }

    public function testGetWithSequentialArrayDefinition()
    {
        $definition = [1, 2, 3];
        $this->container->set(TestDependency::class, $definition);
        $object = $this->container->get(TestDependency::class);

        $this->assertEquals($definition[0], $object->a);
        $this->assertEquals($definition[1], $object->b);
        $this->assertEquals($definition[2], $object->c);
    }

    public function testGetWithClosureDefinition()
    {
        $this->container->set(TestDependency::class, fn() => new TestDependency(1, 2, 3));
        $object = $this->container->get(TestDependency::class);

        $this->assertEquals(1, $object->a);
        $this->assertEquals(2, $object->b);
        $this->assertEquals(3, $object->c);
    }

    public function testGetWithObjectDefinition()
    {
        $initial = new TestDependency(1, 2, 3);
        $this->container->set(TestDependency::class, $initial);
        $found = $this->container->get(TestDependency::class);

        $this->assertEquals($initial, $found);
    }

    public function testSingletonIdFlagged()
    {
        $this->container->singleton(TestDependency::class);
        $singletons = new \ReflectionProperty($this->container, 'singletons');

        $this->assertTrue(isset($singletons->getValue($this->container)[TestDependency::class]));
    }

    public function testSingletonInstanceCached()
    {
        $this->container->singleton(TestDependency::class);
        $object = $this->container->get(TestDependency::class);
        $reflector = new \ReflectionProperty($this->container, 'instances');

        $this->assertEquals($object, $reflector->getValue($this->container)[TestDependency::class]);
    }

    public function testForget()
    {
        $this->container->singleton(TestDependency::class);
        $this->container->get(TestDependency::class);
        $this->container->forget(TestDependency::class);

        $definitions = new \ReflectionProperty($this->container, 'definitions');
        $this->assertFalse(isset($definitions->getValue($this->container)[TestDependency::class]));
        $singletons = new \ReflectionProperty($this->container, 'singletons');
        $this->assertFalse(isset($singletons->getValue($this->container)[TestDependency::class]));
        $instances = new \ReflectionProperty($this->container, 'instances');
        $this->assertFalse(isset($instances->getValue($this->container)[TestDependency::class]));
    }

    public function testGetObjectFromInterface()
    {
        $this->container->set(TestDependency::class, [1, 2, 3]);
        $this->container->set(TestServiceWithInterface::class);
        $object = $this->container->get(TestServiceWithInterface::class);

        $this->assertInstanceOf(TestServiceWithInterface::class, $object);
        $this->assertInstanceOf(TestDependency::class, $object->test);
    }

    public function testGetObjectFromAbstract()
    {
        $this->container->set(TestDependency::class, [1, 2, 3]);
        $this->container->set(TestServiceWithAbstract::class);
        $object = $this->container->get(TestServiceWithAbstract::class);

        $this->assertInstanceOf(TestServiceWithAbstract::class, $object);
        $this->assertInstanceOf(TestDependency::class, $object->test);
    }
}