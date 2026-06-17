<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Customer;

use App\Domain\Customer\Exception\InvalidFICOScoreException;
use App\Domain\Customer\ValueObject\Email;
use App\Domain\Customer\ValueObject\FicoScore;
use App\Domain\Customer\ValueObject\Phone;
use App\Domain\Customer\ValueObject\Ssn;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class CustomerValueObjectsTest extends TestCase
{
    public function testValidValueObjects(): void
    {
        self::assertSame('jane.doe@example.com', (string) new Email('jane.doe@example.com'));
        self::assertSame('5550000001', (string) new Phone('5550000001'));
        self::assertSame('123-45-6789', (string) new Ssn('123-45-6789'));
        self::assertSame(720, (new FicoScore(720))->value);

        self::assertTrue((new Email('a@b.com'))->equals(new Email('a@b.com')));
        self::assertFalse((new Email('a@b.com'))->equals(new Email('c@d.com')));
        self::assertTrue((new FicoScore(800))->isAtLeast(new FicoScore(700)));
    }

    public function testEmailRejectsAnInvalidAddress(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Email('not-an-email');
    }

    public function testPhoneRejectsNonTenDigits(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Phone('555-12');
    }

    public function testSsnRejectsAWrongFormat(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Ssn('123456789');
    }

    public function testFicoScoreRejectsOutOfRange(): void
    {
        $this->expectException(InvalidFICOScoreException::class);

        new FicoScore(299);
    }
}
