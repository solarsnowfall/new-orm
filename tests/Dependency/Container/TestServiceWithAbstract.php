<?php

namespace SSF\Test\Dependency\Container;

class TestServiceWithAbstract
{
    public function __construct(
        public TestDependencyAbstract $test
    ) {}
}