<?php

namespace SSF\ORM\Model;

use SSF\ORM\Model\Attr\Column;
use SSF\ORM\Model\Attr\ColumnKey;
use SSF\ORM\Model\Attr\DataType;

class Permission
{
    #[Column(
        name: 'id',
        dataType: DataType::SmallInt,
        columnKey: ColumnKey::Primary,
        maxLength: 3,
        signed: false
    )]
    protected ?int $id = null;

    #[Column(
        name: 'name',
        dataType: DataType::Varchar,
        maxLength: 40
    )]
    protected ?string $name = null;

    #[Column(
        name: 'description',
        dataType: DataType::Varchar,
        isNullable: true,
        maxLength: 256
    )]
    protected ?string $description = null;
}