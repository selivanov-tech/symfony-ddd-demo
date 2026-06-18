<?php

namespace App\Module\Product\Domain\Entity;

use App\Module\Product\Domain\ValueObject\StateScoreMultiplier;
use App\Module\Product\Domain\ValueObject\StatesScoreMultiplierCollection;
use App\Shared\Domain\Identity\UuidInterface;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'products')]
class Product
{
    #[ORM\Id]
    #[ORM\Column(type: UuidInterface::class, unique: true)]
    private UuidInterface $id;

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

    /**
     * @param string[] $availableStates
     */
    private function __construct(
        UuidInterface $id,
        string $name,
        int $termInMonths,
        float $interestRate,
        float $amount,
        int $minFICOScore,
        int $minMonthlyIncome,
        int $minAge,
        int $maxAge,
        array $availableStates,
        StatesScoreMultiplierCollection $statesScoreMultipliers,
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->termInMonths = $termInMonths;
        $this->interestRate = $interestRate;
        $this->amount = $amount;
        $this->minFICOScore = $minFICOScore;
        $this->minMonthlyIncome = $minMonthlyIncome;
        $this->minAge = $minAge;
        $this->maxAge = $maxAge;
        $this->availableStates = $availableStates;
        $this->statesScoreMultipliers = $statesScoreMultipliers->toArray();
    }

    /**
     * @param string[] $availableStates
     */
    public static function create(
        UuidInterface $id,
        string $name,
        int $termInMonths,
        float $interestRate,
        float $amount,
        int $minFICOScore,
        int $minMonthlyIncome,
        int $minAge,
        int $maxAge,
        array $availableStates,
        StatesScoreMultiplierCollection $statesScoreMultipliers,
    ): self {
        return new self(
            $id,
            $name,
            $termInMonths,
            $interestRate,
            $amount,
            $minFICOScore,
            $minMonthlyIncome,
            $minAge,
            $maxAge,
            $availableStates,
            $statesScoreMultipliers,
        );
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

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

    public function increaseInterestRate(float $amount): void
    {
        $this->interestRate += $amount;
    }

    public function decreaseInterestRate(float $amount): void
    {
        $this->interestRate -= $amount;
    }
}
