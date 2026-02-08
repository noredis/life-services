<?php

declare(strict_types=1);

namespace App\Domain\Failure;

use Exception;
use Throwable;

class ProfileNotFoundException extends Exception
{
    public function __construct(
        public readonly string $email,
        int $code = 0,
        Throwable|null $previous = null,
    ) {
        $message = 'profile not found';

        parent::__construct($message, $code, $previous);
    }
}
