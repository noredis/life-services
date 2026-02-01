<?php

declare(strict_types=1);

namespace App\Application\Constraint;

use Attribute;
use Symfony\Component\Validator\Constraints\Compound;
use Symfony\Component\Validator\Constraints as Assert;

#[Attribute(Attribute::TARGET_PROPERTY)]
class ValidEmail extends Compound
{
    protected function getConstraints(array $options): array
    {
        return [
            new Assert\NotBlank(message: 'email is required'),
            new Assert\Email(message: 'this is not email'),
            new Assert\Length(max: 120, maxMessage: 'email can\'t exceed 120 characters'),
        ];
    }
}
