<?php

declare(strict_types=1);

namespace App\Infrastructure\Helper;

class EmailVerificationTokenGenerator implements EmailVerificationTokenGeneratorInterface
{
    public function generate(): string
    {
        return bin2hex(random_bytes(32));
    }
}
