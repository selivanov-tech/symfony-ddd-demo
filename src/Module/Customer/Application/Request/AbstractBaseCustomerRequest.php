<?php

namespace App\Module\Customer\Application\Request;

use App\Module\Customer\Domain\ValueObject\Address;
use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

abstract class AbstractBaseCustomerRequest
{
    #[Assert\Email]
    #[Assert\NotBlank(groups: ['CustomerCreateRequest'])]
    #[OA\Property(example: 'jane.doe@example.com')]
    public readonly ?string $email;

    #[Assert\Length(min: 10, max: 10)]
    #[Assert\NotBlank(groups: ['CustomerCreateRequest'])]
    #[OA\Property(example: '5550000001')]
    public readonly ?string $phone;

    #[Assert\Date]
    #[Assert\NotBlank(groups: ['CustomerCreateRequest'])]
    #[OA\Property(example: '1990-01-01')]
    public readonly ?string $birthday;

    #[Assert\Length(min: 1, max: 255)]
    #[Assert\NotBlank(groups: ['CustomerCreateRequest'])]
    #[OA\Property(example: 'Jane')]
    public readonly ?string $firstName;

    #[Assert\Length(min: 1, max: 255)]
    #[Assert\NotBlank(groups: ['CustomerCreateRequest'])]
    #[OA\Property(example: 'Doe')]
    public readonly ?string $lastName;

    #[Assert\Regex(
        pattern: '/^\d{3}-\d{2}-\d{4}$/',
        message: 'The SSN must be in the format XXX-XX-XXXX',
    )]
    #[Assert\NotBlank(groups: ['CustomerCreateRequest'])]
    #[OA\Property(example: '123-45-6789')]
    public readonly ?string $ssn;

    #[Assert\Range(
        notInRangeMessage: 'FICO score must be between {{ min }} and {{ max }}.',
        min: 300,
        max: 850,
    )]
    #[Assert\NotBlank(groups: ['CustomerCreateRequest'])]
    #[OA\Property(example: 720)]
    public readonly ?int $ficoScore;

    #[Assert\Type(type: Address::class, message: 'Invalid Address object.')]
    #[Assert\NotBlank(groups: ['CustomerCreateRequest'])]
    #[OA\Property(
        description: 'Postal address',
        example: ['street' => '1 Market St', 'city' => 'San Francisco', 'state' => 'CA', 'zip' => '94105'],
    )]
    public readonly ?Address $address;
    #[Assert\Range(
        notInRangeMessage: 'Monthly income must be more than {{ min }}.',
        min: 0,
    )]
    #[Assert\NotBlank(groups: ['CustomerCreateRequest'])]
    #[OA\Property(example: 6000)]
    public readonly ?int $monthlyIncome;

    public function __construct(array $data)
    {
        $this->email = $data['email'] ?? null;
        $this->phone = $data['phone'] ?? null;
        $this->birthday = $data['birthday'] ?? null;
        $this->firstName = $data['firstName'] ?? null;
        $this->lastName = $data['lastName'] ?? null;
        $this->ssn = $data['ssn'] ?? null;
        // todo: wrap to VO too
        $this->ficoScore = $data['ficoScore'] ?? null;
        $this->address = $data['address'] ? Address::fromRequestData($data['address']) : null;
        $this->monthlyIncome = $data['monthlyIncome'] ?? null;
    }
}
