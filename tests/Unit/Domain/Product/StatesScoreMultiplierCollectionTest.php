<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Product;

use App\Module\Product\Domain\ValueObject\StatesScoreMultiplierCollection;
use PHPUnit\Framework\TestCase;

final class StatesScoreMultiplierCollectionTest extends TestCase
{
    public function testAnEmptyCollectionHasNoRuleForAState(): void
    {
        $collection = new StatesScoreMultiplierCollection([]);

        self::assertNull($collection->getRuleForState('CA'));
    }

    public function testAnEmptyCollectionSerializesToAnEmptyArray(): void
    {
        self::assertSame([], (new StatesScoreMultiplierCollection([]))->toArray());
    }
}
