<?php

declare(strict_types=1);

namespace App\Domain\Interfaces;

use App\Domain\Entity\OutboxMessage;

interface OutboxMessageRepositoryInterface
{
    /**
     * @param array<OutboxMessage> $messages
     */
    public function saveAll(array $messages): array;
    public function save(OutboxMessage $message): OutboxMessage;
    /**
     * @return array<OutboxMessage>
     */
    public function getUnprocessedMessages(int $limit): array;
    public function markAsProcessed(OutboxMessage $message): void;
}
