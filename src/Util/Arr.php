<?php

namespace SSF\ORM\Util;

class Arr
{
    /**
     * @param array $array
     * @return bool
     */
    public static function isAssoc(array $array): bool
    {
        return ! static::isSequential($array);
    }

    /**
     * @param array $array
     * @return bool
     */
    public static function isSequential(array $array): bool
    {
        return [] === $array || array_keys($array) === range(0, count($array) - 1);
    }
}