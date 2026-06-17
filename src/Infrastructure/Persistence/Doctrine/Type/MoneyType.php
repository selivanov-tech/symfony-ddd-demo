<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Type;

use App\Shared\Domain\ValueObject\Money;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;

final class MoneyType extends Type
{
    public const NAME = 'money';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getJsonTypeDeclarationSQL($column);
    }

    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        if (!$value instanceof Money) {
            throw ConversionException::conversionFailedInvalidType($value, self::NAME, ['null', Money::class]);
        }

        return json_encode(
            ['minorUnits' => $value->minorUnits, 'currency' => $value->currency],
            JSON_THROW_ON_ERROR,
        );
    }

    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?Money
    {
        if ($value === null || $value instanceof Money) {
            return $value;
        }

        /** @var array{minorUnits: int, currency: string} $data */
        $data = json_decode((string) $value, true, 512, JSON_THROW_ON_ERROR);

        return new Money((int) $data['minorUnits'], (string) $data['currency']);
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
