<?php

declare(strict_types=1);

namespace App\Presentation\Http\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationFailureEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTExpiredEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTInvalidEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTNotFoundEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class AuthenticationFailureListener
{
    public function __construct(
        private LoggerInterface $logger,
    ) {
    }

    public function onAuthenticationFailureResponse(AuthenticationFailureEvent $event)
    {
        $this->onFailure($event, 'Invalid credentials');
    }

    public function onJWTInvalid(JWTInvalidEvent $event)
    {
        $this->onFailure($event, 'Invalid token');
    }

    public function onJWTNotFound(JWTNotFoundEvent $event)
    {
        $this->onFailure($event, 'Missing token');
    }

    public function onJWTExpired(JWTExpiredEvent $event)
    {
        $this->onFailure($event, 'Token is expired');
    }

    protected function onFailure(AuthenticationFailureEvent $event, string $message): void
    {

        $this->logger->warning('unauthenticated', [
            'message' => $message,
        ]);

        $data = [
            'error' => $message,
        ];

        $response = new JsonResponse(data: $data, status: JsonResponse::HTTP_UNAUTHORIZED);

        $event->setResponse($response);
    }
}
