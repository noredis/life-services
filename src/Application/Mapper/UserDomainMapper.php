<?php

declare(strict_types=1);

namespace App\Application\Mapper;

use App\Application\Request\RegisterRequest;
use App\Application\Response\UserResponse;
use App\Domain\Entity\User;
use DateTimeInterface;

class UserDomainMapper implements UserDomainMapperInterface
{
    public function mapRegisterRequestToEntity(RegisterRequest $request): User
    {
        return new User(
            name: $request->name,
            email: $request->email,
            password: $request->password,
        );
    }

    public function mapUserFromEntity(User $user): UserResponse
    {
        return new UserResponse(
            id: $user->getId(),
            name: $user->getName(),
            email: $user->getEmail(),
            createdAt: $user->getCreatedAt()->format(DateTimeInterface::ISO8601_EXPANDED),
            updatedAt: $user->getUpdatedAt()->format(DateTimeInterface::ISO8601_EXPANDED),
            passwordUpdatedAt: $user->getPasswordUpdatedAt()->format(DateTimeInterface::ISO8601_EXPANDED),
            emailVerifiedAt: $user->getEmailVerifiedAt()
                ? $user->getEmailVerifiedAt()->format(DateTimeInterface::ISO8601_EXPANDED)
                : null,
        );
    }
}
