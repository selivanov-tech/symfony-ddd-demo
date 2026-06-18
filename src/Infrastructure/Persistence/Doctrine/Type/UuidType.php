<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Type;

use App\Shared\Domain\Identity\UuidInterface;
use App\Shared\Infrastructure\Identity\SymfonyUuidFactory;
use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;
use Throwable;

final class UuidType extends Type
{
    public const NAME = 'uuid_binary';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getBinaryTypeDeclarationSQL(['length' => 16, 'fixed' => true]);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?UuidInterface
    {
        if ($value === null || $value instanceof UuidInterface) {
            return $value;
        }

        if (is_resource($value)) {
            $value = stream_get_contents($value);
        }

        if (!is_string($value) || $value === '') {
            return null;
        }

        try {
            return (new SymfonyUuidFactory())->fromBytes($value);
        } catch (Throwable $exception) {
            throw ConversionException::conversionFailed($value, self::NAME, $exception);
        }
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        try {
            if ($value instanceof UuidInterface) {
                return $value->getBytes();
            }

            return (new SymfonyUuidFactory())->fromString((string) $value)->getBytes();
        } catch (Throwable $exception) {
            throw ConversionException::conversionFailed((string) $value, self::NAME, $exception);
        }
    }

    public function getBindingType(): int
    {
        return ParameterType::BINARY;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
