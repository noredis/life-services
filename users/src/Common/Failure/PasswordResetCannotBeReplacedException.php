<?php

declare(strict_types=1);

namespace App\Common\Failure;

use Exception;
use Throwable;

class PasswordResetCannotBeReplacedException extends Exception
{
    public function __construct(int $code = 0, ?Throwable $previous = null)
    {
        $message = 'password reset cannot be replaced now. Try again later';
        return parent::__construct($message, $code, $previous);
    }
}
