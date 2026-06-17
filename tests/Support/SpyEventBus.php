<?php

declare(strict_types=1);

namespace App\Tests\Support;

use App\Shared\Application\Bus\Event\EventBusInterface;
use App\Shared\Domain\Event\DomainEvent;

final class SpyEventBus implements EventBusInterface
{
    /** @var list<DomainEvent> */
    public array $published = [];

    public function publish(DomainEvent ...$events): void
    {
        foreach ($events as $event) {
            $this->published[] = $event;
        }
    }
}
