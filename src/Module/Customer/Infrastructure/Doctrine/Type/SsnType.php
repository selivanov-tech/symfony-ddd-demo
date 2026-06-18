<?php

declare(strict_types=1);

namespace App\Module\Customer\Infrastructure\Doctrine\Type;

use App\Module\Customer\Domain\ValueObject\Ssn;
use App\Shared\Infrastructure\Persistence\Doctrine\Type\StringValueObjectType;

final class SsnType extends StringValueObjectType
{
    public const NAME = Ssn::class;

    protected function fromDatabaseValue(string $value): object
    {
        return new Ssn($value);
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
