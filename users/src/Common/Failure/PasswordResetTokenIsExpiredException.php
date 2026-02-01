<?php

declare(strict_types=1);

namespace App\Common\Failure;

use Exception;
use Throwable;

class PasswordResetTokenIsExpiredException extends Exception
{
    public function __construct(int $code = 0, ?Throwable $previous = null)
    {
        $message = 'password reset token is expired';
        return parent::__construct($message, $code, $previous);
    }
}
