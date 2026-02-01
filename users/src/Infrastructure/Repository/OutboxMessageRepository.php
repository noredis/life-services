<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Entity\OutboxMessage;
use App\Domain\Interfaces\OutboxMessageRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class OutboxMessageRepository extends ServiceEntityRepository implements OutboxMessageRepositoryInterface
{
    public function __construct(ManagerRegistry $registy)
    {
        parent::__construct($registy, OutboxMessage::class);
    }

    public function saveAll(array $messages): array
    {
        foreach ($messages as $message) {
            $this->save($message);
        }

        return $messages;
    }

    public function save(OutboxMessage $message): OutboxMessage
    {
        if (!$message->getId()) {
            $this->getEntityManager()->persist($message);
        }

        $this->getEntityManager()->flush();

        return $message;
    }

    public function getUnprocessedMessages(int $limit): array
    {
        return $this->findBy(
            criteria: ['processedAt' => null],
            orderBy: ['id' => 'DESC'],
            limit: $limit,
        );
    }

    public function markAsProcessed(OutboxMessage $message): void
    {
        $message->process();
        $this->getEntityManager()->flush();
    }
}
