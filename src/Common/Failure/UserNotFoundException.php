<?php

declare(strict_types=1);

namespace App\Common\Failure;

use Exception;
use Throwable;

class UserNotFoundException extends Exception
{
    public function __construct(
        public readonly string $email,
        int $code = 0,
        ?Throwable $previous = null,
    ) {
        $message = "user not found";
        return parent::__construct($message, $code, $previous);
    }
}
