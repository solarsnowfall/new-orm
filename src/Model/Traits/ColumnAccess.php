<?php

namespace SSF\ORM\Model\Traits;

trait ColumnAccess
{
    protected array $guarded = ['id'];

    protected array $hidden = [];

    protected array $readable = ['*'];

    protected array $writeable = ['*'];

    private static array $columnGuarded = [];

    private static array $columnHidden = [];

    private static array $columnReadable = [];

    private static array $columnWriteable = [];

    public function __get(string $name)
    {
        if (static::columnReadable($name)) {
            return $this->getColumn($name);
        }

        return null;
    }

    public function __set(string $name, $value): void
    {
        if (static::columnWriteable($name)) {
            $this->setColumn($name, $value);
        }
    }

    protected static function columnGuarded(string $name): bool
    {
        $class = static::class;

        if (!isset(static::$columnGuarded[$class][$name])) {
            $guarded = static::propertyDefault('guarded');
            static::$columnGuarded[$class][$name] = in_array($name, $guarded);
        }

        return static::$columnGuarded[$class][$name];
    }

    protected static function columnHidden(string $name): bool
    {
        $class = static::class;

        if (!isset(static::$columnHidden[$class][$name])) {
            $hidden = static::propertyDefault('hidden');
            static::$columnHidden[$class][$name] = in_array($name, $hidden);
        }

        return static::$columnHidden[$class][$name];
    }

    protected static function columnReadable(string $name): bool
    {
        $class = static::class;

        if (!isset(static::$columnReadable[$class][$name])) {
            $readable = static::propertyDefault('readable');
            static::$columnReadable[$class][$name] = !static::columnHidden($name)
                && in_array('*', $readable) || in_array($name, $readable);
        }

        return static::$columnReadable[$class][$name];
    }

    protected static function columnWriteable(string $name): bool
    {
        $class = static::class;

        if (!isset(static::$columnWriteable[$class][$name])) {
            $writeable = static::propertyDefault('writeable');
            static::$columnWriteable[$class][$name] = !static::columnHidden($name)
                && !static::columnGuarded($name)
                && in_array('*', $writeable) || in_array($name, $writeable);
        }

        return static::$columnWriteable[$class][$name];
    }
}