<?php

namespace App\Domain\Customer\ValueObject;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Exception\ValidatorException;
use Symfony\Component\Validator\Validation;

#[ORM\Embeddable]
class Address
{
    // todo: load from GeoDictionary instead
    protected const STATES = ['CA', 'NY', 'NV', /* other US states */];
    protected const ZIP_REGEX = '/^\d{5}(-\d{4})?$/';

    #[ORM\Column]
    private string $street;
    #[ORM\Column]
    private string $city;
    #[ORM\Column(length: 2)]
    private string $state;
    #[ORM\Column(length: 10)]
    private string $zip;

    public function __construct(string $street, string $city, string $state, string $zip)
    {
        $this->validateState($state);
        $this->validateZip($zip);

        $this->street = $street;
        $this->city = $city;
        $this->state = $state;
        $this->zip = $zip;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromRequestData(array $data): self
    {
        $constraint = new Assert\Collection([
            'street' => new Assert\NotBlank(),
            'city' => new Assert\NotBlank(),
            'state' => new Assert\Choice([
                'choices' => self::STATES,
                'message' => 'Invalid state. Please use a valid U.S. state abbreviation.',
            ]),
            'zip' => new Assert\Regex([
                'pattern' => self::ZIP_REGEX,
                'message' => 'The ZIP code must be in the format 12345 or 12345-6789.',
            ]),
        ]);

        $violations = Validation::createValidator()->validate($data, $constraint);
        if (count($violations) > 0) {
            throw new ValidatorException((string) $violations);
        }

        return new self($data['street'], $data['city'], $data['state'], $data['zip']);
    }

    private function validateState(string $state): void
    {
        if (!in_array($state, self::STATES, true)) {
            throw new \InvalidArgumentException("Invalid state: $state");
        }
    }

    private function validateZip(string $zip): void
    {
        if (!preg_match(self::ZIP_REGEX, $zip)) {
            throw new \InvalidArgumentException("Invalid ZIP code: $zip");
        }
    }

    public function getStreet(): string
    {
        return $this->street;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function getZip(): string
    {
        return $this->zip;
    }

    /**
     * @return array{street: string, city: string, state: string, zip: string}
     */
    public function toArray(): array
    {
        return [
            'street' => $this->street,
            'city' => $this->city,
            'state' => $this->state,
            'zip' => $this->zip,
        ];
    }
}
