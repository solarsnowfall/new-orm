<?php

namespace SSF\ORM\Model\Attr;

enum ColumnDefault: string
{
    case CurrentTimestamp = 'current_timestamp()';
}