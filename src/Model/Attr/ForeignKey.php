<?php

namespace SSF\ORM\Model\Attr;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class ForeignKey
{
    public function __construct(
        public string $column,
        public string $foreignTable,
        public string $foreignColumn
    ) {}
}