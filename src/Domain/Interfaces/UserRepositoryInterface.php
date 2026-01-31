<?php

declare(strict_types=1);

namespace App\Domain\Interfaces;

use App\Domain\Entity\User;

interface UserRepositoryInterface
{
    public function existsByEmail(string $email): bool;
    public function getByEmail(string $email): ?User;
    public function getByVerificationToken(string $token): ?User;
    public function getByPasswordResetToken(string $token): ?User;
    public function save(User $user): User;
}
