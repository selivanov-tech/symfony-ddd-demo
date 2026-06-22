<?php

declare(strict_types=1);

namespace App\Tests\Builder;

use App\Module\Loan\Domain\ValueObject\ProductTerms;

final class ProductTermsBuilder
{
    private int $minFicoScore = 600;
    private int $minMonthlyIncome = 2000;
    private int $minAge = 18;
    private int $maxAge = 70;
    /** @var string[] */
    private array $availableStates = ['CA', 'NV'];

    public function withMinFicoScore(int $minFicoScore): self
    {
        $this->minFicoScore = $minFicoScore;
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

    public function build(): ProductTerms
    {
        return new ProductTerms(
            $this->minFicoScore,
            $this->minMonthlyIncome,
            $this->minAge,
            $this->maxAge,
            $this->availableStates,
        );
    }
}
