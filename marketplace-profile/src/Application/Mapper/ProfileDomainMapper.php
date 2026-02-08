<?php

declare(strict_types=1);

namespace App\Application\Mapper;

use App\Domain\Entity\Profile;
use App\Application\Response\ProfileResponse;

class ProfileDomainMapper implements ProfileDomainMapperInterface
{
    public function mapFromProfileEntity(Profile $profile): ProfileResponse
    {
        return new ProfileResponse(
            id: $profile->getId(),
            name: $profile->getName(),
            email: $profile->getEmail(),
            isEmailVerified: $profile->isEmailVerified(),
            isSeller: $profile->isSeller(),
        );
    }
}
