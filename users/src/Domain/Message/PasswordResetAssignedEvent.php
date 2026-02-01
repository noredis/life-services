<?php

declare(strict_types=1);

namespace App\Domain\Message;

class PasswordResetAssignedEvent implements AsyncMessageInterface
{
    public string $type = 'user.password_reset.assigned';

    public function __construct(
        public readonly int $userId,
        public readonly string $email,
        public readonly string $resetToken,
    ) {
    }

    public function getPayload(): array
    {
        return [
            'user_id' => $this->userId,
            'email' => $this->email,
            'reset_token' => $this->resetToken,
        ];
    }
}
