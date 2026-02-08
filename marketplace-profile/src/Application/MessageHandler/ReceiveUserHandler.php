<?php

declare(strict_types=1);

namespace App\Application\MessageHandler;

use App\Domain\Entity\Profile;
use App\Domain\Interfaces\ProfileRepositoryInterface;
use App\Domain\Message\UserRegisteredEvent;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class ReceiveUserHandler
{
    public function __construct(
        private ProfileRepositoryInterface $profileRepository,
    ) {
    }

    public function __invoke(UserRegisteredEvent $event): void
    {
        $profile = new Profile(
            userId: $event->userId,
            name: $event->name,
            email: $event->email,
            isEmailVerified: false,
        );

        $this->profileRepository->save($profile);
    }
}
