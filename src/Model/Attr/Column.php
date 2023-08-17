<?php

namespace SSF\ORM\Model\Attr;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Column
{
    public function __construct(
        public string $name,
        public DataType $dataType,
        public mixed $default = null,
        public ?ColumnKey $columnKey = null,
        public bool $isNullable = false,
        public ?int $maxLength = null,
        public ?int $precision = null,
        public ?int $scale = null,
        public ?bool $signed = null
    ) {}
}