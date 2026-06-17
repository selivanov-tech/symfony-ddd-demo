<?php

namespace App\Domain\Product\Entity;

use App\Domain\Product\ValueObject\StateScoreMultiplier;
use App\Domain\Product\ValueObject\StatesScoreMultiplierCollection;
use App\Domain\Shared\Entity\Traits\SharedEntityUuidTrait;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'products')]
class Product
{
    use SharedEntityUuidTrait;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    #[ORM\Column(type: 'integer')]
    private int $termInMonths;

    #[ORM\Column(type: 'decimal', precision: 5, scale: 2)]
    private float $interestRate;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private float $amount;
    #[ORM\Column(options: ['unsigned' => true])]
    private int $minFICOScore;
    #[ORM\Column(options: ['unsigned' => true])]
    private int $minMonthlyIncome;
    #[ORM\Column(options: ['unsigned' => true])]
    private int $minAge;
    #[ORM\Column(options: ['unsigned' => true])]
    private int $maxAge;
    #[ORM\Column]
    private array $availableStates;
    #[ORM\Column(type: 'json')]
    private array $statesScoreMultipliers;

    public function getName(): string
    {
        return $this->name;
    }

    public function getTermInMonths(): int
    {
        return $this->termInMonths;
    }

    public function getInterestRate(): float
    {
        return $this->interestRate;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getMinFICOScore(): int
    {
        return $this->minFICOScore;
    }

    public function getMinMonthlyIncome(): int
    {
        return $this->minMonthlyIncome;
    }

    public function getMinAge(): int
    {
        return $this->minAge;
    }

    public function getMaxAge(): int
    {
        return $this->maxAge;
    }

    public function getAvailableStates(): array
    {
        return $this->availableStates;
    }

    public function getStatesScoreMultipliers(): StatesScoreMultiplierCollection
    {
        $rules = $this->statesScoreMultipliers;

        return new StatesScoreMultiplierCollection(
            array_map(
                static fn (array $rule): StateScoreMultiplier => new StateScoreMultiplier($rule),
                $rules
            )
        );
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function setTermInMonths(int $termInMonths): self
    {
        $this->termInMonths = $termInMonths;
        return $this;
    }

    public function setInterestRate(float $interestRate): self
    {
        $this->interestRate = $interestRate;
        return $this;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;
        return $this;
    }

    public function setMinFICOScore(int $minFICOScore): self
    {
        $this->minFICOScore = $minFICOScore;
        return $this;
    }

    public function setMinMonthlyIncome(int $minMonthlyIncome): self
    {
        $this->minMonthlyIncome = $minMonthlyIncome;
        return $this;
    }

    public function setMinAge(int $minAge): self
    {
        $this->minAge = $minAge;
        return $this;
    }

    public function setMaxAge(int $maxAge): self
    {
        $this->maxAge = $maxAge;
        return $this;
    }

    public function setAvailableStates(array $availableStates): self
    {
        $this->availableStates = $availableStates;
        return $this;
    }

    public function setStatesScoreMultipliers(StatesScoreMultiplierCollection $multipliers): self
    {
        $this->statesScoreMultipliers = $multipliers->toArray();
        return $this;
    }

}
