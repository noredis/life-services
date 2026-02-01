<?php

declare(strict_types=1);

namespace App\Presentation\Http\Api\Controller;

use App\Application\Request\RegisterRequest;
use App\Application\Service\UserServiceInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/auth')]
class AuthController extends AbstractController
{
    public function __construct(
        private UserServiceInterface $userService,
        private LoggerInterface $logger,
    ) {
    }

    #[Route('/register', name: 'register', methods: ['POST'])]
    public function register(Request $request): Response
    {
        $body = $request->toArray();
        $response = $this->userService->register(
            new RegisterRequest(
                email: $body['email'] ?? '',
                name: $body['name'] ?? null,
                password: $body['password'] ?? '',
                passwordConfirm: $body['password_confirm'] ?? '',
            ),
        );

        $this->logger->info('user registered', [
            'id' => $response->id,
            'email' => $response->email,
            'name' => $response->name,
        ]);

        return new JsonResponse(data: $response, status: JsonResponse::HTTP_CREATED);
    }
}
