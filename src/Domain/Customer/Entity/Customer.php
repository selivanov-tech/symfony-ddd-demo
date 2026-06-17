<?php

namespace App\Domain\Customer\Entity;

use App\Domain\Customer\Exception\InvalidFICOScoreException;
use App\Domain\Customer\ValueObject\Address;
use App\Domain\Shared\Entity\Traits\SharedEntityUuidTrait;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Customer
{
    use SharedEntityUuidTrait;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    private string $email;
    #[ORM\Column(type: 'string', length: 10, unique: true)]
    private string $phone;
    #[ORM\Column(type: 'string', length: 11, unique: true)]
    private string $ssn;
    #[ORM\Column(type: 'string', length: 255)]
    private string $firstName;
    #[ORM\Column(type: 'string', length: 255)]
    private string $lastName;
    #[ORM\Column(type: 'date_immutable')]
    private DateTimeImmutable $birthday;
    #[ORM\Column(type: 'integer', options: ['unsigned' => true])]
    private int $ficoScore;
    /**
     * Address fast solution to simplify,
     * better to store it as separate tables, and make relations
     */
    #[ORM\Column(type: 'json')]
    private array $address;
    #[ORM\Column(type: 'integer', options: ['unsigned' => true])]
    private int $monthlyIncome;

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function getSsn(): string
    {
        return $this->ssn;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getPresentedName(): string
    {
        return $this->firstName . ' ' . $this->lastName;
    }

    public function getBirthday(): DateTimeImmutable
    {
        return $this->birthday;
    }

    public function getAge(): int
    {
        // todo: take into account possible timezone divergence, could be calculated on a birthday place
        return $this->birthday->diff(new DateTimeImmutable())->y;
    }

    public function getFicoScore(): int
    {
        return $this->ficoScore;
    }

    public function getAddress(): Address
    {
        return new Address(
            street: $this->address['street'],
            city: $this->address['city'],
            state: $this->address['state'],
            zip: $this->address['zip']
        );
    }

    public function getMonthlyIncome(): int
    {
        return $this->monthlyIncome;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;
        return $this;
    }

    public function setSsn(string $ssn): self
    {
        $this->ssn = $ssn;
        return $this;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;
        return $this;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;
        return $this;
    }

    public function setBirthday(DateTimeImmutable $birthday): self
    {
        $this->birthday = $birthday;
        return $this;
    }

    public function setFicoScore(int $ficoScore): self
    {
        if ($ficoScore < 300 || $ficoScore > 850) {
            throw new InvalidFICOScoreException($ficoScore);
        }

        $this->ficoScore = $ficoScore;

        return $this;
    }

    public function setAddress(array $address): self
    {
        $this->address = $address;
        return $this;
    }

    public function setMonthlyIncome(int $monthlyIncome): self
    {
        $this->monthlyIncome = $monthlyIncome;
        return $this;
    }
}
