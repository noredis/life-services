<?php

declare(strict_types=1);

namespace App\Application\Validator;

use App\Application\Request\UserRequestInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserValidator implements UserValidatorInterface
{
    public function __construct(private ValidatorInterface $validator)
    {
    }

    public function validate(UserRequestInterface $request): array
    {
        $fields = [];

        $violations = $this->validator->validate($request);
        foreach ($violations as $violation) {
            $fields[] = [
                'field' => $violation->getPropertyPath(),
                'error' => $violation->getMessage(),
            ];
        }

        return $fields;
    }
}
