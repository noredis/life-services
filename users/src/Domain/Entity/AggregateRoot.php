<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\Message\AsyncMessageInterface;

abstract class AggregateRoot
{
    /**
     * @var array<AsyncMessageInterface>
     */
    protected array $events = [];

    /**
     * @return array<OutboxMessage>
     */
    public function withdrawEvents(): array
    {
        $events = [];
        foreach ($this->events as $event) {
            $events[] = new OutboxMessage(
                get_class($event),
                $event->getPayload(),
            );
        }

        $this->events = [];

        return $events;
    }

    protected function recordEvent(AsyncMessageInterface $event): void
    {
        $this->events[] = $event;
    }
}
