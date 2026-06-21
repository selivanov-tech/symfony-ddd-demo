<?php

declare(strict_types=1);

namespace App\Tests\Builder;

use App\Module\Customer\Domain\Entity\Customer;
use App\Shared\Domain\Identity\UuidFactoryInterface;
use App\Shared\Domain\Identity\UuidInterface;
use App\Shared\Infrastructure\Identity\SymfonyUuidFactory;
use DateTimeImmutable;

final class CustomerBuilder
{
    private UuidInterface $id;
    private string $email = 'jane.doe@example.com';
    private string $phone = '5550000001';
    private string $ssn = '123-45-6789';
    private string $firstName = 'Jane';
    private string $lastName = 'Doe';
    private DateTimeImmutable $birthday;
    private int $ficoScore = 720;
    /** @var array<string, mixed> */
    private array $address = [
        'street' => '1 Market St',
        'city' => 'San Francisco',
        'state' => 'CA',
        'zip' => '94105',
    ];
    private int $monthlyIncome = 6000;

    public function __construct(?UuidFactoryInterface $uuidFactory = null)
    {
        $this->id = ($uuidFactory ?? new SymfonyUuidFactory())->uuid7();
        $this->birthday = new DateTimeImmutable('1990-01-01');
    }

    public function withId(UuidInterface $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function withEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function withPhone(string $phone): self
    {
        $this->phone = $phone;
        return $this;
    }

    public function withSsn(string $ssn): self
    {
        $this->ssn = $ssn;
        return $this;
    }

    public function withFirstName(string $firstName): self
    {
        $this->firstName = $firstName;
        return $this;
    }

    public function withLastName(string $lastName): self
    {
        $this->lastName = $lastName;
        return $this;
    }

    public function withBirthday(DateTimeImmutable $birthday): self
    {
        $this->birthday = $birthday;
        return $this;
    }

    public function withFicoScore(int $ficoScore): self
    {
        $this->ficoScore = $ficoScore;
        return $this;
    }

    /**
     * @param array<string, mixed> $address
     */
    public function withAddress(array $address): self
    {
        $this->address = $address;
        return $this;
    }

    public function withMonthlyIncome(int $monthlyIncome): self
    {
        $this->monthlyIncome = $monthlyIncome;
        return $this;
    }

    public function build(): Customer
    {
        return Customer::create(
            $this->id,
            $this->email,
            $this->phone,
            $this->ssn,
            $this->firstName,
            $this->lastName,
            $this->birthday,
            $this->ficoScore,
            $this->address,
            $this->monthlyIncome,
        );
    }
}
