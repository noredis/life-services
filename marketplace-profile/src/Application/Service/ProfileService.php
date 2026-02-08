<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Application\Mapper\ProfileDomainMapperInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Application\Response\ProfileResponse;
use App\Domain\Failure\ProfileNotFoundException;
use App\Domain\Interfaces\ProfileRepositoryInterface;

class ProfileService implements ProfileServiceInterface
{
    public function __construct(
        private ProfileRepositoryInterface $profileRepository,
        private ProfileDomainMapperInterface $mapper,
    ) {
    }

    public function whoami(UserInterface $user): ProfileResponse
    {
        $profile = $this->profileRepository->getByEmail($user->getUserIdentifier());
        if (!$profile) {
            throw new ProfileNotFoundException($user->getUserIdentifier());
        }

        return $this->mapper->mapFromProfileEntity($profile);
    }
}
