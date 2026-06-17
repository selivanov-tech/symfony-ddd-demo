<?php

declare(strict_types=1);

namespace App\Tests\Unit\Shared\Domain;

use App\Shared\Domain\ValueObject\Money;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class MoneyTest extends TestCase
{
    public function testFromFloatStoresMinorUnits(): void
    {
        $money = Money::fromFloat(99.95);

        self::assertSame(9995, $money->minorUnits);
        self::assertSame('USD', $money->currency);
        self::assertSame(99.95, $money->toFloat());
    }

    public function testFromFloatRoundsToTheNearestCent(): void
    {
        self::assertSame(1000, Money::fromFloat(9.999)->minorUnits);
    }

    public function testEqualsComparesAmountAndCurrency(): void
    {
        self::assertTrue((new Money(500))->equals(new Money(500)));
        self::assertFalse((new Money(500))->equals(new Money(500, 'EUR')));
        self::assertFalse((new Money(500))->equals(new Money(501)));
    }

    public function testItIsStringable(): void
    {
        self::assertSame('12.50 USD', (string) new Money(1250));
    }

    public function testItRejectsAnInvalidCurrency(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Money(100, 'dollars');
    }
}
