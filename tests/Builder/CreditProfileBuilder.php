<?php

declare(strict_types=1);

namespace App\Tests\Builder;

use App\Module\Loan\Domain\ValueObject\CreditProfile;

final class CreditProfileBuilder
{
    private int $ficoScore = 720;
    private int $monthlyIncome = 6000;
    private int $age = 36;
    private string $state = 'CA';

    public function withFicoScore(int $ficoScore): self
    {
        $this->ficoScore = $ficoScore;
        return $this;
    }

    public function withMonthlyIncome(int $monthlyIncome): self
    {
        $this->monthlyIncome = $monthlyIncome;
        return $this;
    }

    public function withAge(int $age): self
    {
        $this->age = $age;
        return $this;
    }

    public function withState(string $state): self
    {
        $this->state = $state;
        return $this;
    }

    public function build(): CreditProfile
    {
        return new CreditProfile($this->ficoScore, $this->monthlyIncome, $this->age, $this->state);
    }
}
