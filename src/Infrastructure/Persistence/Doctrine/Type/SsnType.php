<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Type;

use App\Domain\Customer\ValueObject\Ssn;

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
