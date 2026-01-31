<?php

declare(strict_types=1);

namespace App\Domain\Message;

class EmailVerifiedEvent implements AsyncMessageInterface
{
    public string $type = 'user.email_verified';

    public function __construct(
        public readonly int $userId,
        public readonly string $email,
    ) {
    }

    public function getPayload(): array
    {
        return [
            'user_id' => $this->userId,
            'email' => $this->email,
        ];
    }
}
