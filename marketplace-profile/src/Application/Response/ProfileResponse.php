<?php

declare(strict_types=1);

namespace App\Application\Response;

class ProfileResponse
{
    public function __construct(
        public readonly int $id,
        public readonly ?string $name,
        public readonly string $email,
        public readonly bool $isEmailVerified,
    ) {
    }
}
