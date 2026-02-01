<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'password_resets')]
#[ORM\HasLifecycleCallbacks]
class PasswordReset
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $token;

    #[ORM\Column]
    private DateTimeImmutable $expiresIn;

    #[ORM\Column]
    private ?DateTimeImmutable $createdAt = null;

    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        $now = new DateTimeImmutable();

        $this->createdAt = $now;
    }

    public function __construct(string $token, DateTimeImmutable $expiresIn)
    {
        $this->token = $token;
        $this->expiresIn = $expiresIn;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function getExpiresIn(): DateTimeImmutable
    {
        return $this->expiresIn;
    }

    public function isExpired(): bool
    {
        $now = new DateTimeImmutable();
        return $now > $this->expiresIn;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function canBeReplaced(): bool
    {
        return true;
    }
}
