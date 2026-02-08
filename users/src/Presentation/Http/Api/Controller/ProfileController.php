<?php

declare(strict_types=1);

namespace App\Presentation\Http\Api\Controller;

use App\Application\Request\EditPersonalInfoRequest;
use App\Application\Service\UserServiceInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;

#[Route('/api/identity/profile')]
class ProfileController extends AbstractController
{
    public function __construct(
        private UserServiceInterface $userService,
        private LoggerInterface $logger,
    ) {
    }

    #[Route('/whoami', name: 'whoami', methods: ['GET'])]
    public function whoami(UserInterface $user): Response
    {
        return $this->json($this->userService->whoami($user), JsonResponse::HTTP_OK);
    }

    #[Route('/edit', name: 'edit-profile', methods: ['PATCH'])]
    public function edit(UserInterface $user, Request $request): Response
    {
        $body = $request->toArray();
        $response = $this->userService->editPersonalInfo(
            user: $user,
            request: new EditPersonalInfoRequest(
                name: $body['name'] ?? null,
            ),
        );

        $this->logger->info('user profile edited', [
            'name' => $response->name,
        ]);

        return $this->json($response, JsonResponse::HTTP_OK);
    }
}
