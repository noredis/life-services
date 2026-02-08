<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Infrastructure\Repository\ProfileRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProfileRepository::class)]
#[ORM\Table(name: 'profiles')]
class Profile extends AggregateRoot
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private int $userId;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name;

    #[ORM\Column(length: 255)]
    private string $email;

    #[ORM\Column]
    private bool $isEmailVerified = false;

    #[ORM\Column]
    private ?DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?DateTimeImmutable $syncedAt = null;

    public function __construct(
        int $userId,
        ?string $name,
        string $email,
        bool $isEmailVerified,
    ) {
        $this->userId = $userId;
        $this->name = $name;
        $this->email = $email;
        $this->isEmailVerified = $isEmailVerified;

        $now = new DateTimeImmutable();
        $this->createdAt = $now;
        $this->syncedAt = $now;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function syncProfileInfo(?string $name): void
    {
        $this->name = $name;
        $this->syncedAt = new DateTimeImmutable();
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function syncEmail(string $email): void
    {
        $this->email = $email;
        $this->syncedAt = new DateTimeImmutable();
    }

    public function isEmailVerified(): bool
    {
        return $this->isEmailVerified;
    }

    public function syncIsEmailVerified(bool $isEmailVerified): void
    {
        $this->isEmailVerified = $isEmailVerified;
        $this->syncedAt = new DateTimeImmutable();
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getSyncedAt(): DateTimeImmutable
    {
        return $this->syncedAt;
    }
}
