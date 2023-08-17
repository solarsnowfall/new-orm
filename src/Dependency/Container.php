<?php

namespace SSF\ORM\Dependency;

use Closure;
use Psr\Container\ContainerInterface;

class Container implements ContainerInterface
{
    private array $definitions = [];

    private array $instances = [];

    private array $singletons = [];

    public function get(string $id): mixed
    {
        if ( ! $this->has($id)) {
            throw new NotFoundException(message: "Dependency not found: $id");
        }

        if (isset($this->singletons[$id])) {
            return ! isset($this->instances[$id])
                ? $this->instances[$id] = $this->resolveInstance($id)
                : $this->instances[$id];
        }

        return $this->resolveInstance($id);
    }

    public function has(string $id): bool
    {
        return array_key_exists($id, $this->definitions);
    }

    public function set(string $id, callable|object|array|null $definition = null, bool $singleton = false): void
    {
        $this->definitions[$id] = $definition;

        if ($singleton) {
            $this->singletons[$id] = true;
        }
    }

    public function singleton(string $id, callable|object|array|null $definition = null): void
    {
        $this->set($id, $definition, true);
    }

    public function forget(string $id): void
    {
        unset($this->definitions[$id], $this->instances[$id], $this->singletons[$id]);
    }

    private function resolveInstance(string $id): mixed
    {
        $definition = $this->definitions[$id];

        if (false === $definition instanceof Closure && is_object($definition)) {
            return $definition;
        }

        if (is_callable($definition)) {
            return call_user_func($definition, [$this]);
        }

        if (class_exists($id) && is_array($definition) || is_null($definition)) {
            return $this->createInstance($id, $definition ?? []);
        }

        return $definition;
    }

    private function createInstance(string $id, array $arguments): mixed
    {
        return (new Resolver($this, $id))->createInstance($arguments);
    }
}