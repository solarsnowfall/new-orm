<?php

namespace SSF\ORM\Dependency;

use Psr\Container\ContainerInterface;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionParameter;
use RuntimeException;
use SSF\ORM\Util\Arr;

class Resolver
{
    /**
     * @param ContainerInterface $container
     * @param ReflectionClass|string $class
     */
    public function __construct(
        private ContainerInterface $container,
        private ReflectionClass|string $class,
    ) {
        if (is_string($this->class)) {
            try {
                $this->class = new ReflectionClass($this->class);
            } catch (ReflectionException $exception) {
                throw new RuntimeException(message: "Class not found: $class", previous: $exception);
            }
        }
    }

    /**
     * @param array $arguments
     * @return mixed
     */
    public function createInstance(array $arguments = []): mixed
    {
        try {
            return $this->class->newInstance(...$this->getDependencies($arguments));
        } catch (ReflectionException $exception) {
            throw new RuntimeException(
                message: sprintf('Unable to resolve dependency: %s', $this->class->getName()),
                previous: $exception
            );
        }

    }

    /**
     * @param array $arguments
     * @return array
     */
    private function getDependencies(array $arguments = []): array
    {
        $constructor = $this->class->getConstructor();

        if (null === $constructor) {
            return [];
        }

        return $this->resolveParameters($constructor, $arguments);
    }

    /**
     * @param ReflectionMethod $constructor
     * @param array $arguments
     * @return array
     */
    private function resolveParameters(ReflectionMethod $constructor, array $arguments): array
    {
        $parameters = [];
        $isSequential = Arr::isSequential($arguments);

        foreach ($constructor->getParameters() as $key => $parameter) {

            $index = $isSequential ? $key : $parameter->getName();

            if (isset($arguments[$index])) {
                $parameters[] = $arguments[$index];
            } elseif (null !== $instance = $this->resolveParameter($parameter)) {
                $parameters[] = $instance;
            } elseif ($parameter->isDefaultValueAvailable()) {
                $parameters[] = $parameter->getDefaultValue();
            }
        }

        return $parameters;
    }

    /**
     * Look through parameter types and check the container for them.
     *
     * @param ReflectionParameter $parameter
     * @return mixed
     */
    private function resolveParameter(ReflectionParameter $parameter): mixed
    {
        $type = $parameter->getType();
        $types = ! $type instanceof ReflectionNamedType
            ? $type->getTypes()
            : [$type];

        if (null !== $instance = $this->findClass($types)) {
            return $instance;
        }

        if (null !== $instance = $this->findImplementing($types)) {
            return $instance;
        }

        return $this->findExtending($types);
    }

    /**
     * Look for exact match in container.
     *
     * @param array $types
     * @return mixed
     */
    private function findClass(array $types): mixed
    {
        foreach ($types as $type) {
            if (class_exists($type->getName()) && $this->container->has($type->getName())) {
                return $this->container->get($type->getName());
            }
        }

        return null;
    }

    /**
     * Look for matching implemented interface in container.
     *
     * @param ReflectionNamedType[] $types
     * @return mixed
     */
    private function findImplementing(array $types): mixed
    {
        foreach ($types as $type) {
            if (interface_exists($type->getName()) && null !== $instance = $this->findInterface($type->getName())) {
                return $instance;
            }
        }

        return null;
    }

    /**
     * @param string $interface
     * @return mixed|null
     */
    private function findInterface(string $interface)
    {
        foreach (get_declared_classes() as $class) {
            if ($this->container->has($class) && in_array($interface, class_implements($class))) {
                return $this->container->get($class);
            }
        }

        return null;
    }

    /**
     * Look for extended parent class in container. This may be getting dubious...
     *
     * @param array $types
     * @return mixed
     */
    private function findExtending(array $types): mixed
    {
        foreach ($types as $type) {
            if (class_exists($type->getName()) && null !== $instance = $this->findParentClass($type->getName())) {
                return $instance;
            }
        }

        return null;
    }

    /**
     * @param string $parent
     * @return mixed|null
     */
    private function findParentClass(string $parent)
    {
        foreach (get_declared_classes() as $class) {
            if ($this->container->has($class) && is_subclass_of($class, $parent)) {
                return $this->container->get($class);
            }
        }

        return null;
    }
}