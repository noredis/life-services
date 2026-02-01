<?php

declare(strict_types=1);

namespace App\Common\Failure;

use Exception;
use Throwable;

class ValidationException extends Exception
{
    public function __construct(
        public readonly array $fields,
        int $code = 0,
        ?Throwable $previous = null,
    ) {
        $message = 'validation failed';
        return parent::__construct($message, $code, $previous);
    }
}
