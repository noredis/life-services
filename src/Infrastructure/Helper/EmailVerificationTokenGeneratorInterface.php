<?php

declare(strict_types=1);

namespace App\Infrastructure\Helper;

interface EmailVerificationTokenGeneratorInterface
{
    public function generate(): string;
}
