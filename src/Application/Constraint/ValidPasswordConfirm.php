<?php

declare(strict_types=1);

namespace App\Application\Constraint;

use Attribute;
use Symfony\Component\Validator\Constraints\Compound;
use Symfony\Component\Validator\Constraints as Assert;

#[Attribute(Attribute::TARGET_PROPERTY)]
class ValidPasswordConfirm extends Compound
{
    public function getConstraints(array $options): array
    {
        return [
            new Assert\EqualTo(propertyPath: 'password', message: 'password mismatch'),
        ];
    }
}
