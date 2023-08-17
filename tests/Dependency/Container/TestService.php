<?php

namespace SSF\Test\Dependency\Container;

class TestService
{
    public function __construct(
        public TestDependency $test
    ) {}
}