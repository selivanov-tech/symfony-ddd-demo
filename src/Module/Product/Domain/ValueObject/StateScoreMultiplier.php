<?php

namespace App\Module\Product\Domain\ValueObject;

use App\Module\Product\Domain\Entity\Product;
use App\Module\Product\Domain\Enum\StateScoreMultiplierOperationEnum;
use InvalidArgumentException;

class StateScoreMultiplier
{
    public readonly string $state;
    public readonly StateScoreMultiplierOperationEnum $operation;
    public readonly int|float $value;

    /**
     * @param array<string, mixed> $rule
     */
    public function __construct(array $rule)
    {
        if (!isset($rule['state'], $rule['operation'], $rule['value'])) {
            throw new InvalidArgumentException('Each rule must have "state", "operation", and "value".');
        }

        $state = $rule['state'];
        $operationValue = $rule['operation'];
        $value = $rule['value'];

        if (!is_string($state)) {
            throw new InvalidArgumentException('State must be a string.');
        }

        if (!is_string($operationValue)) {
            throw new InvalidArgumentException('Operation must be a string.');
        }

        $operation = StateScoreMultiplierOperationEnum::tryFrom($operationValue);
        if ($operation === null) {
            throw new InvalidArgumentException('Invalid operation. Allowed: "plus", "minus".');
        }

        if (!is_numeric($value)) {
            throw new InvalidArgumentException('Value must be numeric.');
        }

        $this->state = $state;
        $this->operation = $operation;
        $this->value = is_int($value) ? $value : (float) $value;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'state' => $this->state,
            'operation' => $this->operation->value,
            'value' => $this->value,
        ];
    }

    public function applyRule(Product $product): void
    {
        $delta = (float) $this->value;

        match ($this->operation) {
            StateScoreMultiplierOperationEnum::PLUS => $product->increaseInterestRate($delta),
            StateScoreMultiplierOperationEnum::MINUS => $product->decreaseInterestRate($delta),
        };
    }
}
