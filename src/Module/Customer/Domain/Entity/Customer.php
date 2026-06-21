<?php

namespace App\Module\Customer\Domain\Entity;

use App\Module\Customer\Domain\ValueObject\Address;
use App\Module\Customer\Domain\ValueObject\Email;
use App\Module\Customer\Domain\ValueObject\FicoScore;
use App\Module\Customer\Domain\ValueObject\Phone;
use App\Module\Customer\Domain\ValueObject\Ssn;
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

    private function __construct(
        UuidInterface $id,
        Email $email,
        Phone $phone,
        Ssn $ssn,
        string $firstName,
        string $lastName,
        DateTimeImmutable $birthday,
        FicoScore $ficoScore,
        Address $address,
        int $monthlyIncome,
    ) {
        $this->id = $id;
        $this->email = $email;
        $this->phone = $phone;
        $this->ssn = $ssn;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->birthday = $birthday;
        $this->ficoScore = $ficoScore;
        $this->address = $address;
        $this->monthlyIncome = $monthlyIncome;
    }

    /**
     * @param array<string, mixed> $address
     */
    public static function create(
        UuidInterface $id,
        string $email,
        string $phone,
        string $ssn,
        string $firstName,
        string $lastName,
        DateTimeImmutable $birthday,
        int $ficoScore,
        array $address,
        int $monthlyIncome,
    ): self {
        return new self(
            $id,
            new Email($email),
            new Phone($phone),
            new Ssn($ssn),
            $firstName,
            $lastName,
            $birthday,
            new FicoScore($ficoScore),
            self::buildAddress($address),
            $monthlyIncome,
        );
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

    /**
     * @param array<string, mixed> $address
     */
    public function changeContactDetails(string $email, string $phone, array $address): void
    {
        $this->email = new Email($email);
        $this->phone = new Phone($phone);
        $this->address = self::buildAddress($address);
    }

    public function rename(string $firstName, string $lastName): void
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
    }

    public function correctBirthday(DateTimeImmutable $birthday): void
    {
        $this->birthday = $birthday;
    }

    public function correctSsn(string $ssn): void
    {
        $this->ssn = new Ssn($ssn);
    }

    public function recordFicoScore(int $ficoScore): void
    {
        $this->ficoScore = new FicoScore($ficoScore);
    }

    public function recordMonthlyIncome(int $monthlyIncome): void
    {
        $this->monthlyIncome = $monthlyIncome;
    }

    /**
     * @param array<string, mixed> $address
     */
    private static function buildAddress(array $address): Address
    {
        return new Address(
            (string) $address['street'],
            (string) $address['city'],
            (string) $address['state'],
            (string) $address['zip'],
        );
    }
}
