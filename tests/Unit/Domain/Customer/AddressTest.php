<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Customer;

use App\Domain\Customer\ValueObject\Address;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Exception\ValidatorException;

final class AddressTest extends TestCase
{
    public function testItBuildsAValidAddress(): void
    {
        $address = new Address('1 Market St', 'San Francisco', 'CA', '94105');

        self::assertSame('1 Market St', $address->getStreet());
        self::assertSame('San Francisco', $address->getCity());
        self::assertSame('CA', $address->getState());
        self::assertSame('94105', $address->getZip());
    }

    public function testItRejectsAnUnknownState(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new Address('1 Market St', 'Austin', 'TX', '94105');
    }

    public function testItRejectsAMalformedZip(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new Address('1 Market St', 'San Francisco', 'CA', 'not-a-zip');
    }

    public function testFromRequestDataBuildsAnAddress(): void
    {
        $address = Address::fromRequestData([
            'street' => '1 Market St',
            'city' => 'San Francisco',
            'state' => 'CA',
            'zip' => '94105-1234',
        ]);

        self::assertSame('CA', $address->getState());
        self::assertSame('94105-1234', $address->getZip());
    }

    public function testFromRequestDataRejectsInvalidData(): void
    {
        $this->expectException(ValidatorException::class);

        Address::fromRequestData([
            'street' => '1 Market St',
            'city' => 'San Francisco',
            'state' => 'TX',
            'zip' => '94105',
        ]);
    }

    public function testToArrayRoundTripsTheData(): void
    {
        $data = [
            'street' => '1 Market St',
            'city' => 'San Francisco',
            'state' => 'CA',
            'zip' => '94105',
        ];

        self::assertSame($data, Address::fromRequestData($data)->toArray());
    }
}
