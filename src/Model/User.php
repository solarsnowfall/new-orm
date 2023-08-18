<?php

namespace SSF\ORM\Model;

use SSF\ORM\Model\Attr\Column;
use SSF\ORM\Model\Attr\ColumnDefault;
use SSF\ORM\Model\Attr\ColumnKey;
use SSF\ORM\Model\Attr\DataType;

class User extends Model
{
    #[Column(
        name: 'id',
        dataType: DataType::MediumInt,
        columnKey: ColumnKey::Primary,
        maxLength: 8,
        signed: false
    )]
    protected ?int $id = null;

    #[Column(
        name: 'email',
        dataType: DataType::Varchar,
        columnKey: ColumnKey::Unique,
        maxLength: 256
    )]
    protected ?string $email = null;

    #[Column(
        name: 'username',
        dataType: DataType::Varchar,
        maxLength: 40
    )]
    protected ?string $username = null;

    #[Column(
        name: 'updated_at',
        dataType: DataType::Datetime,
        default: ColumnDefault::CurrentTimestamp,
    )]
    protected ?string $updated_at = null;
}