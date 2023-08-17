<?php

namespace SSF\ORM\Model\Traits;

use InvalidArgumentException;
use ReflectionException;
use ReflectionProperty;

trait GetsPropertyDefault
{
    /**
     * @var array
     */
    private static array $propertyDefaults = [];

    /**
     * @param string $name
     * @return mixed
     */
    public static function propertyDefault(string $name): mixed
    {
        $class = static::class;

        if (!isset(static::$propertyDefaults[$class][$name])) {

            try {
                $reflector = new ReflectionProperty(static::class, $name);
            } catch (ReflectionException $exception) {
                throw new InvalidArgumentException(
                    message:"Property not found: $name",
                    previous: $exception
                );
            }

            static::$propertyDefaults[$class][$name] = $reflector->hasDefaultValue()
                ? $reflector->getDefaultValue()
                : null;
        }

        return static::$propertyDefaults[$class][$name];
    }
}