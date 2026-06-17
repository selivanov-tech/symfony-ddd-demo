<?php

declare(strict_types=1);

namespace App\Tests\Unit\Shared\Infrastructure\Bus;

use App\Shared\Application\Bus\Command\CommandInterface;
use App\Shared\Application\Bus\Query\QueryInterface;
use App\Shared\Infrastructure\Bus\Messenger\MessengerCommandBus;
use App\Shared\Infrastructure\Bus\Messenger\MessengerQueryBus;
use App\Tests\Support\RecordingMessageBus;
use PHPUnit\Framework\TestCase;

final class MessengerCommandQueryBusTest extends TestCase
{
    public function testCommandBusDispatchesAndReturnsTheHandlerResult(): void
    {
        $messageBus = new RecordingMessageBus(handlerResult: 'new-id');
        $command = new class () implements CommandInterface {};

        $result = (new MessengerCommandBus($messageBus))->dispatch($command);

        self::assertSame('new-id', $result);
        self::assertSame([$command], $messageBus->dispatched);
    }

    public function testQueryBusDispatchesAndReturnsTheReadModel(): void
    {
        $readModel = (object) ['id' => 'q-1'];
        $messageBus = new RecordingMessageBus(handlerResult: $readModel);
        $query = new class () implements QueryInterface {};

        $result = (new MessengerQueryBus($messageBus))->ask($query);

        self::assertSame($readModel, $result);
        self::assertSame([$query], $messageBus->dispatched);
    }
}
