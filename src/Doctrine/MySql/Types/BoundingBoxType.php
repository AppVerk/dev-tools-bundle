<?php

declare(strict_types = 1);

namespace DevTools\Doctrine\MySql\Types;

use DevTools\Location\BoundingBox;
use DevTools\Location\Point;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class BoundingBoxType extends Type implements SpatialTypeInterface
{
    public const BOUNDING_BOX = 'bounding_box';

    public function getName()
    {
        return self::BOUNDING_BOX;
    }

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return 'POLYGON';
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if (null === $value) {
            return $value;
        }

        $coordinates = sscanf($value, 'POLYGON((%f %f,%f %f,%f %f,%f %f,%f %f))');

        return new BoundingBox(
            new Point($coordinates[0], $coordinates[1]),
            new Point($coordinates[4], $coordinates[5])
        );
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value instanceof BoundingBox) {
            $value = sprintf(
                'POLYGON((%f %f,%f %f,%f %f,%f %f,%f %f))',
                $value->getLeftBottom()->getLongitude(),
                $value->getLeftBottom()->getLatitude(),
                $value->getLeftTop()->getLongitude(),
                $value->getLeftTop()->getLatitude(),
                $value->getRightTop()->getLongitude(),
                $value->getRightTop()->getLatitude(),
                $value->getRightBottom()->getLongitude(),
                $value->getRightBottom()->getLatitude(),
                $value->getLeftBottom()->getLongitude(),
                $value->getLeftBottom()->getLatitude()
            );
        }

        return $value;
    }

    public function canRequireSQLConversion(): bool
    {
        return true;
    }

    public function convertToPHPValueSQL($sqlExpr, $platform)
    {
        return sprintf('ST_AsText(%s)', $sqlExpr);
    }

    public function convertToDatabaseValueSQL($sqlExpr, AbstractPlatform $platform)
    {
        return sprintf('ST_PolygonFromText(%s)', $sqlExpr);
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform)
    {
        return true;
    }
}
