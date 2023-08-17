<?php

namespace SSF\Test\Dependency\Container;

class TestServiceWithInterface
{
    public function __construct(
        public TestDependencyInterface $test
    ) {}
}