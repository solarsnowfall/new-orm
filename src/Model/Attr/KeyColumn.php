<?php

namespace SSF\ORM\Model\Attr;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class KeyColumn
{
    public function __construct(
        public string $table,
        public string $column
    ) {}
}