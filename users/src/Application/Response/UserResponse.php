<?php

declare(strict_types=1);

namespace App\Application\Response;

class UserResponse
{
    public function __construct(
        public readonly int $id,
        public readonly string $email,
        public readonly ?string $name,
        public readonly string $createdAt,
        public readonly string $updatedAt,
        public readonly string $passwordUpdatedAt,
        public readonly ?string $emailVerifiedAt,
    ) {
    }
}
