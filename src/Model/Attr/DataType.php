<?php

namespace SSF\ORM\Model\Attr;

enum DataType: string
{
    case BigInt = 'BIGINT';
    case Bit = 'BIT';
    case Decimal = 'DECIMAL';
    case Double = 'DOUBLE';
    case Float = 'FLOAT';
    case Int = 'INT';
    case MediumInt = 'MEDIUMINT';
    case Numeric = 'NUMERIC';
    case SmallInt = 'SMALLINT';
    case TinyInt = 'TINYINT';

    case Date = 'DATE';
    case Datetime = 'DATETIME';
    case Time = 'TIME';
    case Timestamp = 'TIMESTAMP';
    case Year = 'YEAR';

    case Binary = 'BINARY';
    case Blob = 'BLOB';
    case Char = 'CHAR';
    case Enum = 'ENUM';
    case LongBlob = 'LONGBLOB';
    case LongText = 'LONGTEXT';
    case MediumBlob = 'MEDIUMBLOB';
    case MediumText = 'MEDIUMTEXT';
    case Set = 'SET';
    case TinyBlob = 'TINYBLOB';
    case TinyText = 'TINYTEXT';
    case Varbinary = 'VARBINARY';
    case Varchar = 'VARCHAR';

    case LineString = 'LINESTRING';
    case MultiLineStrong = 'MULTILINESTRING';
    case MultiPoint = 'MULTIPOINT';
    case MultiPolygon = 'MULTIPOLYGON';
    case Geometry = 'GEOMETRY';
    case GeometryCollection = 'GEOMETRYCOLLECTION';
    case Point = 'POINT';
    case Polygon = 'POLYGON';

    case JSON = 'JSON';
}
