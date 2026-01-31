<?php

declare(strict_types=1);

namespace App\Domain\UseCase;

use App\Domain\Interfaces\UserRepositoryInterface;

class IsEmailAvailable
{
    public function __construct(private UserRepositoryInterface $userRepository)
    {
    }

    public function __invoke(string $email): bool
    {
        return !$this->userRepository->existsByEmail($email);
    }
}
