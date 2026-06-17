<?php

declare(strict_types=1);

namespace App\Tests\Unit\Shared\Identity;

use App\Shared\Domain\Identity\UuidFactoryInterface;
use App\Shared\Domain\Identity\UuidInterface;
use App\Shared\Infrastructure\Identity\SymfonyUuidFactory;
use PHPUnit\Framework\TestCase;

final class UuidTest extends TestCase
{
    private UuidFactoryInterface $factory;

    protected function setUp(): void
    {
        $this->factory = new SymfonyUuidFactory();
    }

    public function testUuid7ProducesACanonicalStringAnd16Bytes(): void
    {
        $uuid = $this->factory->uuid7();

        self::assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-7[0-9a-f]{3}-[0-9a-f]{4}-[0-9a-f]{12}$/',
            $uuid->toString(),
        );
        self::assertSame($uuid->toString(), (string) $uuid);
        self::assertSame(16, strlen($uuid->getBytes()));
    }

    public function testItRoundTripsThroughStringAndBytes(): void
    {
        $uuid = $this->factory->uuid7();

        self::assertTrue($uuid->equals($this->factory->fromString($uuid->toString())));
        self::assertTrue($uuid->equals($this->factory->fromBytes($uuid->getBytes())));
    }

    public function testEquals(): void
    {
        $uuid = $this->factory->uuid7();

        self::assertTrue($uuid->equals($this->factory->fromString($uuid->toString())));
        self::assertFalse($uuid->equals($this->factory->uuid7()));
        self::assertFalse($uuid->equals(null));
        self::assertFalse($uuid->equals(new \stdClass()));
    }

    public function testTheFactoryReturnsTheDomainContract(): void
    {
        self::assertInstanceOf(UuidInterface::class, $this->factory->uuid7());
    }
}
