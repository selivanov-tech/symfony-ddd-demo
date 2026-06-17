<?php

declare(strict_types=1);

namespace App\Tests\Unit\Shared\Infrastructure\Bus;

use App\Shared\Domain\Event\AbstractDomainEvent;
use App\Shared\Domain\Event\DomainEvent;
use App\Shared\Infrastructure\Bus\Messenger\MessengerEventBus;
use App\Tests\Support\RecordingMessageBus;
use PHPUnit\Framework\TestCase;

final class MessengerEventBusTest extends TestCase
{
    public function testItDispatchesEveryEventToTheUnderlyingBus(): void
    {
        $messageBus = new RecordingMessageBus();
        $bus = new MessengerEventBus($messageBus);

        $first = $this->event('agg-1');
        $second = $this->event('agg-2');

        $bus->publish($first, $second);

        self::assertSame([$first, $second], $messageBus->dispatched);
    }

    public function testPublishingNothingDispatchesNothing(): void
    {
        $messageBus = new RecordingMessageBus();

        (new MessengerEventBus($messageBus))->publish();

        self::assertSame([], $messageBus->dispatched);
    }

    private function event(string $aggregateId): DomainEvent
    {
        return new class ($aggregateId) extends AbstractDomainEvent {
            public function eventName(): string
            {
                return 'test.happened';
            }
        };
    }
}
