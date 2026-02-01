<?php

declare(strict_types=1);

namespace App\Application\Constraint;

use Attribute;
use Symfony\Component\Validator\Constraints\Compound;
use Symfony\Component\Validator\Constraints as Assert;

#[Attribute(Attribute::TARGET_PROPERTY)]
class ValidPassword extends Compound
{
    public function getConstraints(array $options): array
    {
        return [
            new Assert\NotBlank(message: 'password is required'),
            new Assert\PasswordStrength(),
            new Assert\Length(
                min: 14,
                max: 40,
                minMessage: 'password must be at least 14 characters',
                maxMessage: 'password can\'t exceed 40 characters',
            ),
        ];
    }
}
