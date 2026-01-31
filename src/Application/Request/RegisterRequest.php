<?php

declare(strict_types=1);

namespace App\Application\Request;

use App\Application\Constraint\ValidEmail;
use App\Application\Constraint\ValidName;
use App\Application\Constraint\ValidPassword;
use App\Application\Constraint\ValidPasswordConfirm;

class RegisterRequest implements UserRequestInterface
{
    public function __construct(
        #[ValidEmail]
        public readonly string $email,
        #[ValidName]
        public readonly ?string $name,
        #[ValidPassword]
        public readonly string $password,
        #[ValidPasswordConfirm]
        public readonly string $passwordConfirm,
    ) {
    }
}
