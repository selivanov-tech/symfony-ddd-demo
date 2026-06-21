<?php

declare(strict_types=1);

namespace App\Module\Customer\Infrastructure\Doctrine\Type;

use App\Module\Customer\Domain\ValueObject\Phone;
use App\Shared\Infrastructure\Persistence\Doctrine\Type\StringValueObjectType;

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
