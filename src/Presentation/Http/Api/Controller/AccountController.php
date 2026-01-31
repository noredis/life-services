<?php

declare(strict_types=1);

namespace App\Presentation\Http\Api\Controller;

use App\Application\Request\ChangeEmailRequest;
use App\Application\Request\ChangePasswordRequest;
use App\Application\Request\RequestResetPasswordRequest;
use App\Application\Service\UserServiceInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;

#[Route('/api/account')]
class AccountController extends AbstractController
{
    public function __construct(
        private UserServiceInterface $userService,
        private LoggerInterface $logger,
    ) {
    }

    #[Route('/email/change', name: 'change-email', methods: ['POST'])]
    public function changeEmail(UserInterface $user, Request $request): Response
    {
        $body = $request->toArray();
        $response = $this->userService->changeEmail(
            user: $user,
            request: new ChangeEmailRequest(
                email: $body['email'] ?? '',
            ),
        );

        $this->logger->info('user email changed', [
            'email' => $response->email,
        ]);

        return new JsonResponse(data: $response, status: JsonResponse::HTTP_OK);
    }

    #[Route('/email/verification/request', name: 'request-email-verification', methods: ['POST'])]
    public function requestEmailVerification(UserInterface $user): Response
    {
        $this->userService->requestEmailVerification($user);

        $this->logger->info('email verification requested');

        return new JsonResponse(status: JsonResponse::HTTP_NO_CONTENT);
    }

    #[Route('/email/verify/{token}', name: 'verify-email', methods: ['GET'])]
    public function verifyEmail(string $token): Response
    {
        $response = $this->userService->verifyEmail($token);

        $this->logger->info('email verified', [
            'email' => $response->email,
        ]);

        return new JsonResponse(data: $response, status: JsonResponse::HTTP_OK);
    }

    #[Route('/password/change', name: 'change-password', methods: ['POST'])]
    public function changePassword(UserInterface $user, Request $request): Response
    {
        $body = $request->toArray();
        $response = $this->userService->changePassword(
            user: $user,
            request: new ChangePasswordRequest(
                password: $body['password'] ?? '',
                passwordConfirm: $body['password_confirm'] ?? '',
            ),
        );

        $this->logger->info('password changed');

        return new JsonResponse(data: $response, status: JsonResponse::HTTP_OK);
    }

    #[Route('/password/reset/request', name: 'request-password-reset', methods: ['POST'])]
    public function requestPasswordReset(Request $request): Response
    {
        $body = $request->toArray();
        $this->userService->requestPasswordReset(
            new RequestResetPasswordRequest(
                email: $body['email'] ?? '',
            ),
        );

        $this->logger->info('password reset requested');

        return new JsonResponse(status: JsonResponse::HTTP_NO_CONTENT);
    }

    #[Route('/password/reset/{token}', name: 'reset-password', methods: ['POST'])]
    public function resetPassword(string $token, Request $request): Response
    {
        $body = $request->toArray();
        $response = $this->userService->resetPassword(
            token: $token,
            request: new ChangePasswordRequest(
                password: $body['password'] ?? '',
                passwordConfirm: $body['password_confirm'] ?? '',
            ),
        );

        $this->logger->info('password reseted');

        return new JsonResponse(data: $response, status: JsonResponse::HTTP_OK);
    }
}
