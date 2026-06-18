<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Persistence\Doctrine\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

/**
 * Maps a Stringable value object to a single string column. Subclasses only say
 * how to rebuild the value object from its stored string.
 */
abstract class StringValueObjectType extends Type
{
    abstract protected function fromDatabaseValue(string $value): object;

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getStringTypeDeclarationSQL($column);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?object
    {
        if ($value === null) {
            return null;
        }

        return $this->fromDatabaseValue((string) $value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        return (string) $value;
    }
}
