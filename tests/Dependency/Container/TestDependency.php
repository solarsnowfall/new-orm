<?php

namespace SSF\Test\Dependency\Container;

class TestDependency extends TestDependencyAbstract implements TestDependencyInterface
{
    public function __construct(
        public ?int $a = null,
        public ?int $b = null,
        public ?int $c = null
    ) {}
}