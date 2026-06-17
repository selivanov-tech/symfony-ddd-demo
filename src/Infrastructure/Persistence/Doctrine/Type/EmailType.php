<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Type;

use App\Domain\Customer\ValueObject\Email;

final class EmailType extends StringValueObjectType
{
    public const NAME = Email::class;

    protected function fromDatabaseValue(string $value): object
    {
        return new Email($value);
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
