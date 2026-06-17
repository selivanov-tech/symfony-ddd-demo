<?php

declare(strict_types=1);

namespace App\Tests\Support;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

/**
 * In-memory test double for a Messenger bus. It records every dispatched message
 * and stamps the envelope with a single HandledStamp so HandleTrait-based adapters
 * (command/query bus) can read back a handler result.
 */
final class RecordingMessageBus implements MessageBusInterface
{
    /** @var list<object> */
    public array $dispatched = [];

    public function __construct(
        private readonly mixed $handlerResult = null,
    ) {
    }

    public function dispatch(object $message, array $stamps = []): Envelope
    {
        $envelope = $message instanceof Envelope ? $message : new Envelope($message);
        $this->dispatched[] = $envelope->getMessage();

        return $envelope->with(new HandledStamp($this->handlerResult, 'fake.handler'));
    }
}
