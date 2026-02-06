<?php

declare(strict_types=1);

namespace App\Domain\Message;

class EmailVerificationAssignedEvent implements AsyncMessageInterface
{
    public string $type = 'user.email_verification.assigned';

    public function __construct(
        public readonly string $email,
        public readonly string $verificationToken,
    ) {
    }

    public function getPayload(): array
    {
        return [
            'email' => $this->email,
            'verification_token' => $this->verificationToken,
        ];
    }
}
