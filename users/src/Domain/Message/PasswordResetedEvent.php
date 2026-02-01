<?php

declare(strict_types=1);

namespace App\Domain\Message;

class PasswordResetedEvent implements AsyncMessageInterface
{
    public string $type = 'user.password_reseted';

    public function __construct(
        public readonly int $userId,
    ) {
    }

    public function getPayload(): array
    {
        return [
            'user_id' => $this->userId,
        ];
    }
}
