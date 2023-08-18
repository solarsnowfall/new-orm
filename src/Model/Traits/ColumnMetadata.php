<?php

namespace SSF\ORM\Model\Traits;

use ReflectionAttribute;
use ReflectionClass;
use SSF\ORM\Model\Attr\Column;

trait ColumnMetadata
{
    /**
     * @var Column[]
     */
    private static array $columMetadata = [];

    /**
     * @return Column[]
     */
    public static function columnMetadata(): array
    {
        $class = static::class;

        if (!isset(static::$columMetadata[$class])) {

            static::$columMetadata[$class] = [];
            $reflector = new ReflectionClass($class);

            foreach ($reflector->getProperties() as $property) {

                $attributes = $property->getAttributes(Column::class, ReflectionAttribute::IS_INSTANCEOF);

                if (!empty($attributes)) {

                    $column = $attributes[0]->newInstance();

                    if (!$column->alias && $column->name !== $property->getName()) {
                        $column->alias = $property->getName();
                    }

                    static::$columMetadata[$class][$property->getName()] = $attributes[0]->newInstance();
                }
            }
        }

        return static::$columMetadata[$class];
    }
}