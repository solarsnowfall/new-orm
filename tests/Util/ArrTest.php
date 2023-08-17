<?php

namespace SSF\Test\Util;

use PHPUnit\Framework\TestCase;
use SSF\ORM\Util\Arr;

class ArrTest extends TestCase
{
    public function testIsAssoc()
    {
        $assoc = ['a' => 1, 'b' => 2, 'c' => 3];
        $this->assertTrue(Arr::isAssoc($assoc));
    }

    public function testIsSequential()
    {
        $sequential = [1, 2, 3];
        $this->assertTrue(Arr::isSequential($sequential));
    }
}