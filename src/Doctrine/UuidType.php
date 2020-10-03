<?php

declare(strict_types=1);

namespace App\Doctrine;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\GuidType;
use InvalidArgumentException;
use Symfony\Component\Uid\Uuid;

/**
 * Class UuidType
 * @package App\Doctrine
 */
class UuidType extends GuidType
{
    public const NAME = "uuid";

    public function getName(): string
    {
        return static::NAME;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if ($value === null || $value === '') {
            return null;
        }

        if ($value instanceof Uuid) {
            return $value;
        }

        try {
            $uuid = Uuid::fromString($value);
        } catch (InvalidArgumentException $e) {
            throw new ConversionException($value, static::NAME);
        }

        return $uuid;
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (
            $value instanceof Uuid
            || (
                (is_string($value)
                    || method_exists($value, '__toString'))
                && Uuid::isValid($value)
            )
        ) {
            return (string) $value;
        }

        throw ConversionException::conversionFailed($value, static::NAME);
    }
}
