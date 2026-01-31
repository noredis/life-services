<?php

declare(strict_types=1);

namespace App\Domain\Message;

class UserRegisteredEvent implements AsyncMessageInterface
{
    public string $type = 'user.registered';

    public function __construct(
        public readonly int $userId,
        public readonly string $email,
        public readonly ?string $name,
    ) {
    }

    public function getPayload(): array
    {
        return [
            'user_id' => $this->userId,
            'email' => $this->email,
            'name' => $this->name,
        ];
    }
}
