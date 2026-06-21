<?php

declare(strict_types=1);

namespace App\Module\Customer\Infrastructure\Doctrine\Type;

use App\Module\Customer\Domain\ValueObject\Email;
use App\Shared\Infrastructure\Persistence\Doctrine\Type\StringValueObjectType;

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
