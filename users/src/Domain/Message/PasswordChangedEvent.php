<?php

declare(strict_types=1);

namespace App\Domain\Message;

class PasswordChangedEvent implements AsyncMessageInterface
{
    public string $type = 'user.password_changed';

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
