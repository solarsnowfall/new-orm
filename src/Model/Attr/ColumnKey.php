<?php

namespace SSF\ORM\Model\Attr;

enum ColumnKey: string
{
    case Multi = 'MUL';
    case Primary = 'PRI';
    case Unique = 'UNI';
}
