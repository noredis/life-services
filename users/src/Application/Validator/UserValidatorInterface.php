<?php

declare(strict_types=1);

namespace App\Application\Validator;

use App\Application\Request\UserRequestInterface;

interface UserValidatorInterface
{
    public function validate(UserRequestInterface $request): array;
}
