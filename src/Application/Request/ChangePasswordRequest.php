<?php

declare(strict_types=1);

namespace App\Application\Request;

use App\Application\Constraint\ValidPassword;
use App\Application\Constraint\ValidPasswordConfirm;

class ChangePasswordRequest implements UserRequestInterface
{
    public function __construct(
        #[ValidPassword]
        public readonly string $password,
        #[ValidPasswordConfirm]
        public readonly string $passwordConfirm,
    ) {
    }
}
