<?php

namespace App\Domain\Customer\Entity;

use App\Domain\Customer\ValueObject\Address;
use App\Domain\Customer\ValueObject\Email;
use App\Domain\Customer\ValueObject\FicoScore;
use App\Domain\Customer\ValueObject\Phone;
use App\Domain\Customer\ValueObject\Ssn;
use App\Shared\Domain\Identity\UuidInterface;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Customer
{
    #[ORM\Id]
    #[ORM\Column(type: UuidInterface::class, unique: true)]
    private UuidInterface $id;

    #[ORM\Column(type: Email::class, length: 255, unique: true)]
    private Email $email;
    #[ORM\Column(type: Phone::class, length: 10, unique: true)]
    private Phone $phone;
    #[ORM\Column(type: Ssn::class, length: 11, unique: true)]
    private Ssn $ssn;
    #[ORM\Column(type: 'string', length: 255)]
    private string $firstName;
    #[ORM\Column(type: 'string', length: 255)]
    private string $lastName;
    #[ORM\Column(type: 'date_immutable')]
    private DateTimeImmutable $birthday;
    #[ORM\Column(type: FicoScore::class)]
    private FicoScore $ficoScore;
    #[ORM\Embedded(class: Address::class)]
    private Address $address;
    #[ORM\Column(type: 'integer', options: ['unsigned' => true])]
    private int $monthlyIncome;

    public function __construct(UuidInterface $id)
    {
        $this->id = $id;
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getPhone(): Phone
    {
        return $this->phone;
    }

    public function getSsn(): Ssn
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

    public function getFicoScore(): FicoScore
    {
        return $this->ficoScore;
    }

    public function getAddress(): Address
    {
        return $this->address;
    }

    public function getMonthlyIncome(): int
    {
        return $this->monthlyIncome;
    }

    public function setEmail(string $email): self
    {
        $this->email = new Email($email);
        return $this;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = new Phone($phone);
        return $this;
    }

    public function setSsn(string $ssn): self
    {
        $this->ssn = new Ssn($ssn);
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
        $this->ficoScore = new FicoScore($ficoScore);
        return $this;
    }

    /**
     * @param array<string, mixed> $address
     */
    public function setAddress(array $address): self
    {
        $this->address = new Address(
            (string) $address['street'],
            (string) $address['city'],
            (string) $address['state'],
            (string) $address['zip'],
        );
        return $this;
    }

    public function setMonthlyIncome(int $monthlyIncome): self
    {
        $this->monthlyIncome = $monthlyIncome;
        return $this;
    }
}
