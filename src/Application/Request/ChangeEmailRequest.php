<?php

declare(strict_types=1);

namespace App\Application\Request;

use App\Application\Constraint\ValidEmail;

class ChangeEmailRequest implements UserRequestInterface
{
    public function __construct(
        #[ValidEmail]
        public readonly string $email,
    ) {
    }
}
