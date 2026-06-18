<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Customer;

use App\Module\Customer\Domain\Exception\InvalidFICOScoreException;
use App\Module\Customer\Domain\ValueObject\Address;
use App\Tests\Builder\CustomerBuilder;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class CustomerTest extends TestCase
{
    public function testItAcceptsAValidFicoScore(): void
    {
        $customer = (new CustomerBuilder())->withFicoScore(700)->build();

        self::assertSame(700, $customer->getFicoScore()->value);
    }

    /**
     * @return list<array{int}>
     */
    public static function invalidFicoScores(): array
    {
        return [[299], [851], [0], [1000]];
    }

    #[DataProvider('invalidFicoScores')]
    public function testItRejectsAnOutOfRangeFicoScore(int $score): void
    {
        $this->expectException(InvalidFICOScoreException::class);

        (new CustomerBuilder())->withFicoScore($score)->build();
    }

    public function testItComputesAgeFromBirthday(): void
    {
        $customer = (new CustomerBuilder())->withBirthday(new \DateTimeImmutable('1980-01-01'))->build();

        self::assertGreaterThanOrEqual(40, $customer->getAge());
    }

    public function testItExposesThePresentedName(): void
    {
        $customer = (new CustomerBuilder())->withFirstName('Jane')->withLastName('Doe')->build();

        self::assertSame('Jane Doe', $customer->getPresentedName());
    }

    public function testItExposesTheAddressValueObject(): void
    {
        $customer = (new CustomerBuilder())->withAddress([
            'street' => '1 Market St',
            'city' => 'San Francisco',
            'state' => 'CA',
            'zip' => '94105',
        ])->build();

        $address = $customer->getAddress();

        self::assertInstanceOf(Address::class, $address);
        self::assertSame('CA', $address->getState());
    }
}
