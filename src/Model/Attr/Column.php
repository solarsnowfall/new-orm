<?php

namespace SSF\ORM\Model\Attr;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Column
{
    public function __construct(
        public string $name,
        public DataType $dataType,
        public bool $isNullable = false,
        public mixed $default = null,
        public ?ColumnKey $columnKey = null,
        public ?int $maxLength = null,
        public ?int $precision = null,
        public ?int $scale = null,
        public ?bool $signed = null,
        public ?string $alias = null
    ) {}
}