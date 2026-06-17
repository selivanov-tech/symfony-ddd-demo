<?php

declare(strict_types=1);

namespace App\Tests\Unit\Shared\Domain;

use App\Shared\Domain\Aggregate\AggregateRoot;
use App\Shared\Domain\Event\AbstractDomainEvent;
use App\Shared\Domain\Event\DomainEvent;
use PHPUnit\Framework\TestCase;

final class AggregateRootTest extends TestCase
{
    public function testItHasNoEventsByDefault(): void
    {
        self::assertSame([], (new RecordingAggregate())->releaseEvents());
    }

    public function testItRecordsAndReleasesEvents(): void
    {
        $aggregate = new RecordingAggregate();
        $event = $this->newEvent('agg-1');

        $aggregate->emit($event);

        self::assertSame([$event], $aggregate->releaseEvents());
    }

    public function testReleasingClearsTheBufferSoEventsAreNotDispatchedTwice(): void
    {
        $aggregate = new RecordingAggregate();
        $aggregate->emit($this->newEvent('agg-1'));

        $aggregate->releaseEvents();

        self::assertSame([], $aggregate->releaseEvents());
    }

    private function newEvent(string $aggregateId): DomainEvent
    {
        return new class ($aggregateId) extends AbstractDomainEvent {
            public function eventName(): string
            {
                return 'test.happened';
            }
        };
    }
}

final class RecordingAggregate extends AggregateRoot
{
    public function emit(DomainEvent $event): void
    {
        $this->recordEvent($event);
    }
}
