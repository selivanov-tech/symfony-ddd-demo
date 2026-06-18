<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Type;

use App\Domain\Customer\ValueObject\FicoScore;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

final class FicoScoreType extends Type
{
    public const NAME = FicoScore::class;

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getIntegerTypeDeclarationSQL($column);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?FicoScore
    {
        if ($value === null) {
            return null;
        }

        return new FicoScore((int) $value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?int
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof FicoScore) {
            return $value->value;
        }

        return (int) $value;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
