<?php

declare(strict_types=1);

namespace App\Common\Failure;

use Exception;
use Throwable;

class EmailIsBusyException extends Exception
{
    public function __construct(
        public readonly string $email,
        int $code = 0,
        ?Throwable $previous = null,
    ) {
        $message = 'email is busy';
        return parent::__construct($message, $code, $previous);
    }
}
