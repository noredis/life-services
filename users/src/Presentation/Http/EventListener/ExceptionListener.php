<?php

declare(strict_types=1);

namespace App\Presentation\Http\EventListener;

use App\Common\Failure\EmailIsAlreadyVerifiedException;
use App\Common\Failure\EmailIsBusyException;
use App\Common\Failure\EmailVerificationCannotBeReplacedException;
use App\Common\Failure\PasswordResetCannotBeReplacedException;
use App\Common\Failure\PasswordResetNotFoundException;
use App\Common\Failure\PasswordResetTokenIsExpiredException;
use App\Common\Failure\TokenNotFoundException;
use App\Common\Failure\UserNotFoundException;
use App\Common\Failure\ValidationException;
use App\Common\Failure\VerificationTokenIsExpiredException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class ExceptionListener
{
    public function __construct(
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $message = $exception->getMessage();

        $data = ['error' => $message];

        $response = match (true) {
            $exception instanceof ValidationException => $this->handleValidationException($exception, $data),
            $exception instanceof EmailIsBusyException => $this->handleEmailIsBusyException($exception, $data),
            $exception instanceof EmailIsAlreadyVerifiedException
            => $this->handleEmailIsAlreadyVerifiedException($exception, $data),
            $exception instanceof EmailVerificationCannotBeReplacedException
            => $this->handleEmailVerificationCannotBeReplacedException($data),
            $exception instanceof VerificationTokenIsExpiredException
            => $this->handleVerificationTokenIsExpiredException($data),
            $exception instanceof TokenNotFoundException => $this->handleTokenNotFoundException($data),
            $exception instanceof PasswordResetCannotBeReplacedException
            => $this->handlePasswordResetCannotBeReplacedException($data),
            $exception instanceof PasswordResetTokenIsExpiredException
            => $this->handlePasswordResetTokenIsExpiredException($data),
            $exception instanceof UserNotFoundException => $this->handleUserNotFoundException($exception, $data),
            $exception instanceof PasswordResetNotFoundException => $this->handlePasswordResetNotFoundException($data),
            $exception instanceof HttpExceptionInterface => $this->handleHttpException($exception, $data),
            default => $this->handleInternalServerException($exception, $data),
        };

        $event->setResponse($response);
    }

    protected function handleValidationException(
        ValidationException $e,
        array $data,
    ): JsonResponse {
        $this->logger->warning('validation failed', [
            'fields' => $e->fields,
        ]);

        $data['fields'] = $e->fields;
        return new JsonResponse(data: $data, status: JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
    }

    protected function handleEmailIsBusyException(
        EmailIsBusyException $e,
        array $data,
    ): JsonResponse {
        $this->logger->warning('email is busy', [
            'email' => $e->email,
        ]);

        return new JsonResponse(data: $data, status: JsonResponse::HTTP_CONFLICT);
    }

    protected function handleEmailIsAlreadyVerifiedException(
        EmailIsAlreadyVerifiedException $e,
        array $data,
    ): JsonResponse {
        $this->logger->warning('email is already verified', [
            'email' => $e->email,
        ]);

        return new JsonResponse(data: $data, status: JsonResponse::HTTP_CONFLICT);
    }

    protected function handleVerificationTokenIsExpiredException(
        array $data,
    ): JsonResponse {
        $this->logger->warning('verification token is expired');

        return new JsonResponse(data: $data, status: JsonResponse::HTTP_GONE);
    }

    protected function handleTokenNotFoundException(array $data): JsonResponse
    {
        $this->logger->warning('token not found');

        return new JsonResponse(data: $data, status: JsonResponse::HTTP_NOT_FOUND);
    }

    protected function handleEmailVerificationCannotBeReplacedException(
        array $data,
    ): JsonResponse {
        $this->logger->warning('email verification cannot be replaced');

        return new JsonResponse(data: $data, status: JsonResponse::HTTP_TOO_MANY_REQUESTS);
    }

    protected function handlePasswordResetCannotBeReplacedException(
        array $data,
    ): JsonResponse {
        $this->logger->warning('password reset cannot be replaced');

        return new JsonResponse(data: $data, status: JsonResponse::HTTP_TOO_MANY_REQUESTS);
    }

    protected function handlePasswordResetTokenIsExpiredException(
        array $data,
    ): JsonResponse {
        $this->logger->warning('password reset token is expired');

        return new JsonResponse(data: $data, status: JsonResponse::HTTP_GONE);
    }

    protected function handlePasswordResetNotFoundException(
        array $data,
    ): JsonResponse {
        $this->logger->warning('password reset not found');

        return new JsonResponse(data: $data, status: JsonResponse::HTTP_NOT_FOUND);
    }

    protected function handleUserNotFoundException(
        UserNotFoundException $e,
        array $data,
    ): JsonResponse {
        $this->logger->warning('user not found', [
            'email' => $e->email,
        ]);

        return new JsonResponse(data: $data, status: JsonResponse::HTTP_NOT_FOUND);
    }

    protected function handleHttpException(
        HttpExceptionInterface $e,
        array $data,
    ): JsonResponse {
        if ($e->getStatusCode() === JsonResponse::HTTP_INTERNAL_SERVER_ERROR) {
            return $this->handleInternalServerException($e, $data);
        }

        $this->logger->warning($e->getMessage());

        return new JsonResponse(data: $data, status: $e->getStatusCode());
    }

    protected function handleInternalServerException(
        Throwable $t,
        array $data,
    ): JsonResponse {
        $this->logger->error($t->getMessage());

        $data['error'] = 'internal server error';
        return new JsonResponse(data: $data, status: JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
    }
}
