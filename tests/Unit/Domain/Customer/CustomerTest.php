<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Customer;

use App\Domain\Customer\Entity\Customer;
use App\Domain\Customer\Exception\InvalidFICOScoreException;
use App\Domain\Customer\ValueObject\Address;
use App\Shared\Domain\Identity\UuidFactoryInterface;
use App\Shared\Infrastructure\Identity\SymfonyUuidFactory;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class CustomerTest extends TestCase
{
    private UuidFactoryInterface $uuid;

    protected function setUp(): void
    {
        $this->uuid = new SymfonyUuidFactory();
    }

    public function testItAcceptsAValidFicoScore(): void
    {
        $customer = (new Customer($this->uuid->uuid7()))->setFicoScore(700);

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

        (new Customer($this->uuid->uuid7()))->setFicoScore($score);
    }

    public function testItComputesAgeFromBirthday(): void
    {
        $customer = (new Customer($this->uuid->uuid7()))->setBirthday(new \DateTimeImmutable('1980-01-01'));

        self::assertGreaterThanOrEqual(40, $customer->getAge());
    }

    public function testItExposesThePresentedName(): void
    {
        $customer = (new Customer($this->uuid->uuid7()))
            ->setFirstName('Jane')
            ->setLastName('Doe');

        self::assertSame('Jane Doe', $customer->getPresentedName());
    }

    public function testItRebuildsTheAddressValueObject(): void
    {
        $customer = (new Customer($this->uuid->uuid7()))->setAddress([
            'street' => '1 Market St',
            'city' => 'San Francisco',
            'state' => 'CA',
            'zip' => '94105',
        ]);

        $address = $customer->getAddress();

        self::assertInstanceOf(Address::class, $address);
        self::assertSame('CA', $address->getState());
    }
}
