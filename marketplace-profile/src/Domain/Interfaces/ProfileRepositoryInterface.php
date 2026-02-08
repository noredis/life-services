<?php

declare(strict_types=1);

namespace App\Domain\Interfaces;

use App\Domain\Entity\Profile;

interface ProfileRepositoryInterface
{
    public function getByEmail(string $email): ?Profile;
    public function save(Profile $profile): Profile;
}
