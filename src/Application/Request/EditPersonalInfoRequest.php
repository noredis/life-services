<?php

declare(strict_types=1);

namespace App\Application\Request;

use App\Application\Constraint\ValidName;

class EditPersonalInfoRequest implements UserRequestInterface
{
    public function __construct(
        #[ValidName]
        public readonly ?string $name,
    ) {
    }
}
