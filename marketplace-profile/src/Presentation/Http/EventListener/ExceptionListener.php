<?php

declare(strict_types=1);

namespace App\Presentation\Http\EventListener;

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
            $exception instanceof HttpExceptionInterface => $this->handleHttpException($exception, $data),
            default => $this->handleInternalServerException($exception, $data),
        };

        $event->setResponse($response);
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
