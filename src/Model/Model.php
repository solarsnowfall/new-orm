<?php

namespace SSF\ORM\Model;

use InvalidArgumentException;
use SSF\ORM\Model\Attr\ColumnKey;
use SSF\ORM\Model\Traits\ColumnAccess;
use SSF\ORM\Model\Traits\ColumnMetadata;
use SSF\ORM\Model\Traits\GetsPropertyDefault;
use SSF\ORM\Util\Str;

abstract class Model
{
    use ColumnMetadata;
    use ColumnAccess;
    use GetsPropertyDefault;

    /**
     * @var string
     */
    public string $primaryKey = 'id';

    /**
     * @var string
     */
    public string $table;

    /**
     * @var array
     */
    private array $initialColumns = [];

    /**
     * @param array $columns
     */
    public function __construct(array $columns = [])
    {
        $this->initialize($columns);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return json_encode($this->getColumns());
    }

    /**
     * @param array $columns
     * @return void
     */
    private function initialize(array $columns): void
    {
        $this->setColumns($columns);

        foreach (static::columns() as $name) {
            $this->initialColumns[$name] = $columns[$name] ?? null;
        }
    }

    /**
     * @return array
     */
    public static function columns(): array
    {
        $columns = [];
        foreach (static::columnMetadata() as $column) {
            $columns[] = $column->name;
        }

        return $columns;
    }

    /**
     * @param string $name
     * @return bool
     */
    public static function hasColumn(string $name): bool
    {
        return in_array($name, static::columns());
    }

    public function getColumn(string $name): mixed
    {
        if (static::hasColumn($name)) {
            return $this->$name;
        }

        return null;
    }

    /**
     * @return array
     */
    public function getColumns(): array
    {
        $columns = [];
        foreach (static::columns() as $name) {
            $columns[$name] = $this->$name;
        }

        return $columns;
    }

    /**
     * @return array
     */
    public function updatedColumns(): array
    {
        $columns = [];
        foreach (static::columns() as $name) {
            if ($this->$name !== $this->initialColumns[$name]) {
                $columns[$name] = $this->$name;
            }
        }

        return $columns;
    }

    /**
     * @param array $values
     * @return void
     */
    public function setColumns(array $values): void
    {
        foreach (static::columns() as $name) {
            if (isset($values[$name])) {
                $this->setColumn($name, $values[$name]);
            }
        }
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public function setColumn(string $name, mixed $value): void
    {
        if ( ! static::hasColumn($name)) {
            throw new InvalidArgumentException("Column property not defined: $name");
        }

        $this->$name = $value;
    }

    /**
     * @return ?string
     */
    public static function id(): ?string
    {
        foreach (static::columnMetadata() as $column) {
            if ($column->columnKey === ColumnKey::Primary) {
                return $column->name;
            }
        }

        return null;
    }

    /**
     * @return string
     */
    public static function table(): string
    {
        if (null !== $table = static::propertyDefault('table')) {
            return $table;
        }

        return Str::plural(Str::snake(Str::class(static::class, false), false));
    }
}