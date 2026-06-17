<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Product;

use App\Domain\Product\Entity\Product;
use App\Domain\Product\Enum\StateScoreMultiplierOperationEnum;
use App\Domain\Product\ValueObject\StateScoreMultiplier;
use App\Shared\Domain\Identity\UuidFactoryInterface;
use App\Shared\Infrastructure\Identity\SymfonyUuidFactory;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class StateScoreMultiplierTest extends TestCase
{
    private UuidFactoryInterface $uuid;

    protected function setUp(): void
    {
        $this->uuid = new SymfonyUuidFactory();
    }

    public function testItBuildsFromAValidRule(): void
    {
        $multiplier = new StateScoreMultiplier(['state' => 'CA', 'operation' => 'plus', 'value' => 2.5]);

        self::assertSame('CA', $multiplier->state);
        self::assertSame(StateScoreMultiplierOperationEnum::PLUS, $multiplier->operation);
        self::assertSame(2.5, $multiplier->value);
    }

    public function testApplyRulePlusIncreasesTheInterestRate(): void
    {
        $product = (new Product($this->uuid->uuid7()))->setInterestRate(10.0);

        (new StateScoreMultiplier(['state' => 'CA', 'operation' => 'plus', 'value' => 2.5]))->applyRule($product);

        self::assertSame(12.5, $product->getInterestRate());
    }

    public function testApplyRuleMinusDecreasesTheInterestRate(): void
    {
        $product = (new Product($this->uuid->uuid7()))->setInterestRate(10.0);

        (new StateScoreMultiplier(['state' => 'CA', 'operation' => 'minus', 'value' => 3.0]))->applyRule($product);

        self::assertSame(7.0, $product->getInterestRate());
    }

    public function testToArraySerializesTheOperationToItsScalarValue(): void
    {
        $multiplier = new StateScoreMultiplier(['state' => 'NV', 'operation' => 'minus', 'value' => 1]);

        self::assertSame(['state' => 'NV', 'operation' => 'minus', 'value' => 1], $multiplier->toArray());
    }

    /**
     * @return array<string, array{array<string, mixed>}>
     */
    public static function invalidRules(): array
    {
        return [
            'missing keys' => [['state' => 'CA']],
            'invalid operation' => [['state' => 'CA', 'operation' => 'times', 'value' => 1]],
            'non-numeric value' => [['state' => 'CA', 'operation' => 'plus', 'value' => 'abc']],
        ];
    }

    /**
     * @param array<string, mixed> $rule
     */
    #[DataProvider('invalidRules')]
    public function testItRejectsInvalidRules(array $rule): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new StateScoreMultiplier($rule);
    }
}
