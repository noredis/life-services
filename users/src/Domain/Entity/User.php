<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Common\Failure\EmailIsAlreadyVerifiedException;
use App\Common\Failure\EmailVerificationCannotBeReplacedException;
use App\Common\Failure\PasswordResetCannotBeReplacedException;
use App\Common\Failure\PasswordResetNotFoundException;
use App\Common\Failure\PasswordResetTokenIsExpiredException;
use App\Common\Failure\VerificationTokenIsExpiredException;
use App\Domain\Message\EmailChangedEvent;
use App\Domain\Message\EmailVerificationAssignedEvent;
use App\Domain\Message\EmailVerifiedEvent;
use App\Domain\Message\PasswordChangedEvent;
use App\Domain\Message\PasswordResetAssignedEvent;
use App\Domain\Message\PasswordResetedEvent;
use App\Domain\Message\PersonalInfoEditedEvent;
use App\Domain\Message\UserRegisteredEvent;
use App\Infrastructure\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'users')]
#[ORM\HasLifecycleCallbacks]
class User extends AggregateRoot implements PasswordAuthenticatedUserInterface, UserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private string $email;

    #[ORM\Column(length: 255)]
    private string $password;

    #[ORM\Column]
    private ?DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?DateTimeImmutable $updatedAt = null;

    #[ORM\Column]
    private ?DateTimeImmutable $passwordUpdatedAt = null;

    #[ORM\Column(nullable: true)]
    private ?DateTimeImmutable $emailVerifiedAt = null;

    #[ORM\OneToOne(targetEntity: EmailVerification::class, cascade: ['persist'], orphanRemoval: true)]
    #[ORM\JoinColumn(name: 'email_verification_id', referencedColumnName: 'id')]
    private ?EmailVerification $emailVerification = null;

    #[ORM\OneToOne(targetEntity: PasswordReset::class, cascade: ['persist'], orphanRemoval: true)]
    #[ORM\JoinColumn(name: 'password_reset_id', referencedColumnName: 'id')]
    private ?PasswordReset $passwordReset = null;

    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        $now = new DateTimeImmutable();

        $this->createdAt = $now;
        $this->updatedAt = $now;
        $this->passwordUpdatedAt = $now;
    }

    #[ORM\PostPersist]
    public function onPostPersist(): void
    {
        $this->recordEvent(
            new UserRegisteredEvent(
                userId: $this->id,
                email: $this->email,
                name: $this->name,
            ),
        );
    }

    #[ORM\PreUpdate]
    public function onPreUpdate(PreUpdateEventArgs $args): void
    {
        $now = new DateTimeImmutable();

        $this->updatedAt = $now;

        if ($args->hasChangedField('password')) {
            $this->passwordUpdatedAt = $now;
        }
    }

    public function __construct(?string $name, string $email, string $password)
    {
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function editPersonalInfo(?string $name): void
    {
        $oldName = $this->name;
        $this->name = $name;

        $this->recordEvent(
            new PersonalInfoEditedEvent(
                userId: $this->id,
                name: $name,
                oldName: $oldName,
            ),
        );
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function changeEmail(string $email, EmailVerification $emailVerification): void
    {
        $oldEmail = $this->email;
        $this->email = $email;
        $this->emailVerifiedAt = null;

        $this->recordEvent(
            new EmailChangedEvent(
                userId: $this->id,
                email: $email,
                oldEmail: $oldEmail,
            ),
        );

        $this->assignEmailVerification($emailVerification);
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function changePassword(string $password): void
    {
        $this->password = $password;

        if ($this->id !== null) {
            $this->recordEvent(
                new PasswordChangedEvent(
                    userId: $this->id,
                ),
            );
        }
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function getPasswordUpdatedAt(): ?DateTimeImmutable
    {
        return $this->passwordUpdatedAt;
    }

    public function getEmailVerifiedAt(): ?DateTimeImmutable
    {
        return $this->emailVerifiedAt;
    }

    public function isEmailVerified(): bool
    {
        return $this->emailVerifiedAt !== null;
    }

    public function getEmailVerification(): EmailVerification
    {
        return $this->emailVerification;
    }

    public function hasEmailVerification(): bool
    {
        return $this->emailVerification !== null;
    }

    public function assignEmailVerification(EmailVerification $emailVerification): void
    {
        if ($this->isEmailVerified()) {
            throw new EmailIsAlreadyVerifiedException($this->email);
        }

        if ($this->hasEmailVerification() && !$this->emailVerification->canBeReplaced()) {
            throw new EmailVerificationCannotBeReplacedException($this->email);
        }

        $this->emailVerification = $emailVerification;

        $this->recordEvent(
            new EmailVerificationAssignedEvent(
                email: $this->email,
                verificationToken: $emailVerification->getToken(),
            ),
        );
    }

    public function verifyEmail(): void
    {
        if ($this->isEmailVerified()) {
            throw new EmailIsAlreadyVerifiedException($this->email);
        }

        if ($this->emailVerification->isExpired()) {
            throw new VerificationTokenIsExpiredException();
        }

        $this->emailVerifiedAt = new DateTimeImmutable();

        $this->recordEvent(
            new EmailVerifiedEvent(
                userId: $this->id,
                email: $this->email,
            ),
        );
    }

    public function getPasswordReset(): ?PasswordReset
    {
        return $this->passwordReset;
    }

    public function hasPasswordReset(): bool
    {
        return $this->passwordReset !== null;
    }

    public function assignPasswordReset(PasswordReset $passwordReset): void
    {
        if ($this->hasPasswordReset() && !$this->passwordReset->canBeReplaced()) {
            throw new PasswordResetCannotBeReplacedException();
        }

        $this->passwordReset = $passwordReset;

        $this->recordEvent(
            new PasswordResetAssignedEvent(
                userId: $this->id,
                email: $this->email,
                resetToken: $passwordReset->getToken(),
            ),
        );
    }

    public function resetPassword(): void
    {
        if (!$this->hasPasswordReset()) {
            throw new PasswordResetNotFoundException();
        }

        if ($this->passwordReset->isExpired()) {
            throw new PasswordResetTokenIsExpiredException();
        }

        $this->recordEvent(
            new PasswordResetedEvent(
                userId: $this->id,
            ),
        );

        $this->passwordReset = null;
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function getRoles(): array
    {
        return ['ROLE_USER'];
    }
}
