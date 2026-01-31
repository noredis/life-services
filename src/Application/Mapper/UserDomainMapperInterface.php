<?php

declare(strict_types=1);

namespace App\Application\Mapper;

use App\Application\Request\RegisterRequest;
use App\Application\Response\UserResponse;
use App\Domain\Entity\User;

interface UserDomainMapperInterface
{
    public function mapRegisterRequestToEntity(RegisterRequest $request): User;
    public function mapUserFromEntity(User $user): UserResponse;
}
