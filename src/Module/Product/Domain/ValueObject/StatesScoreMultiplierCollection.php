<?php

namespace App\Module\Product\Domain\ValueObject;

use Symfony\Component\Validator\Constraints as Assert;

class StatesScoreMultiplierCollection
{
    public function __construct(
        #[Assert\All(
            new Assert\Type(StateScoreMultiplier::class),
        )]
        /** @var StateScoreMultiplier[] */
        public readonly array $rules
    ) {
    }

    public function toArray(): array
    {
        $rules = $this->rules;

        return array_map(
            static fn (StateScoreMultiplier $rule) => $rule->toArray(),
            $rules
        );
    }

    public function getRuleForState(string $state): ?StateScoreMultiplier
    {
        foreach ($this->rules as $rule) {
            if ($rule->state === $state) {
                return $rule;
            }
        }

        return null;
    }
}
