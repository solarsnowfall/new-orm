<?php

namespace SSF\ORM\Util;

class Arr
{
    public static function isAssoc(array $array): bool
    {
        return ! static::isSequential($array);
    }

    public static function isSequential(array $array): bool
    {
        return [] === $array || array_keys($array) === range(0, count($array) - 1);
    }
}