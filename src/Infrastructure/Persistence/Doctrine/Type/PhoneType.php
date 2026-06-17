<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Type;

use App\Domain\Customer\ValueObject\Phone;

final class PhoneType extends StringValueObjectType
{
    public const NAME = Phone::class;

    protected function fromDatabaseValue(string $value): object
    {
        return new Phone($value);
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
