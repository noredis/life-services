<?php

declare(strict_types=1);

namespace App\Presentation\Http\Api\Controller;

use App\Application\Service\ProfileServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;

#[Route('/api/marketplace/profile')]
class ProfileController extends AbstractController
{
    public function __construct(
        private ProfileServiceInterface $profileService,
    ) {
    }

    #[Route('/whoami', name: 'whoami', methods: ['GET'])]
    public function whoami(UserInterface $user): JsonResponse
    {
        return $this->json($this->profileService->whoami($user), JsonResponse::HTTP_OK);
    }
}
