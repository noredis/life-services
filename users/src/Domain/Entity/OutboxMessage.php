<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Infrastructure\Repository\OutboxMessageRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OutboxMessageRepository::class)]
#[ORM\Table('outbox_messages')]
#[ORM\HasLifecycleCallbacks]
class OutboxMessage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $type;

    #[ORM\Column(type: 'json')]
    private array $payload;

    #[ORM\Column]
    private ?DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?DateTimeImmutable $processedAt = null;

    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        $now = new DateTimeImmutable();

        $this->createdAt = $now;
    }

    public function __construct(string $type, array $payload)
    {
        $this->type = $type;
        $this->payload = $payload;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getPayload(): array
    {
        return $this->payload;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getProcessedAt(): ?DateTimeImmutable
    {
        return $this->processedAt;
    }

    public function isProcessed(): bool
    {
        return $this->processedAt !== null;
    }

    public function process(): void
    {
        $now = new DateTimeImmutable();

        $this->processedAt = $now;
    }
}
