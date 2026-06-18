<?php

declare(strict_types=1);

namespace App\Tests\Builder;

use App\Domain\Product\Entity\Product;
use App\Domain\Product\ValueObject\StatesScoreMultiplierCollection;
use App\Shared\Domain\Identity\UuidFactoryInterface;
use App\Shared\Domain\Identity\UuidInterface;
use App\Shared\Infrastructure\Identity\SymfonyUuidFactory;

final class ProductBuilder
{
    private UuidInterface $id;
    private string $name = 'Personal Loan';
    private int $termInMonths = 24;
    private float $interestRate = 9.5;
    private float $amount = 10000.0;
    private int $minFICOScore = 600;
    private int $minMonthlyIncome = 2000;
    private int $minAge = 18;
    private int $maxAge = 70;
    /** @var string[] */
    private array $availableStates = ['CA', 'NV'];
    private StatesScoreMultiplierCollection $statesScoreMultipliers;

    public function __construct(?UuidFactoryInterface $uuidFactory = null)
    {
        $this->id = ($uuidFactory ?? new SymfonyUuidFactory())->uuid7();
        $this->statesScoreMultipliers = new StatesScoreMultiplierCollection([]);
    }

    public function withId(UuidInterface $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function withName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function withTermInMonths(int $termInMonths): self
    {
        $this->termInMonths = $termInMonths;
        return $this;
    }

    public function withInterestRate(float $interestRate): self
    {
        $this->interestRate = $interestRate;
        return $this;
    }

    public function withAmount(float $amount): self
    {
        $this->amount = $amount;
        return $this;
    }

    public function withMinFICOScore(int $minFICOScore): self
    {
        $this->minFICOScore = $minFICOScore;
        return $this;
    }

    public function withMinMonthlyIncome(int $minMonthlyIncome): self
    {
        $this->minMonthlyIncome = $minMonthlyIncome;
        return $this;
    }

    public function withMinAge(int $minAge): self
    {
        $this->minAge = $minAge;
        return $this;
    }

    public function withMaxAge(int $maxAge): self
    {
        $this->maxAge = $maxAge;
        return $this;
    }

    /**
     * @param string[] $availableStates
     */
    public function withAvailableStates(array $availableStates): self
    {
        $this->availableStates = $availableStates;
        return $this;
    }

    public function withStatesScoreMultipliers(StatesScoreMultiplierCollection $statesScoreMultipliers): self
    {
        $this->statesScoreMultipliers = $statesScoreMultipliers;
        return $this;
    }

    public function build(): Product
    {
        return Product::create(
            $this->id,
            $this->name,
            $this->termInMonths,
            $this->interestRate,
            $this->amount,
            $this->minFICOScore,
            $this->minMonthlyIncome,
            $this->minAge,
            $this->maxAge,
            $this->availableStates,
            $this->statesScoreMultipliers,
        );
    }
}
