<?php

declare(strict_types=1);

namespace App\Common\Logger;

use Monolog\LogRecord;
use Monolog\Processor\ProcessorInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class RequestProcessor implements ProcessorInterface
{
    private ?string $requestId = null;

    public function __construct(
        private RequestStack $requestStack
    ) {
    }

    public function __invoke(LogRecord $record): LogRecord
    {
        $request = $this->requestStack->getCurrentRequest();

        if (!$request) {
            return $record;
        }

        if ($this->requestId === null) {
            $this->requestId = $this->generateRequestId();
        }

        $record->extra['request_id'] = $this->requestId;
        $record->extra['url'] = $request->getUri();
        $record->extra['method'] = $request->getMethod();
        $record->extra['ip'] = $request->getClientIp();
        $record->extra['user_agent'] = $request->headers->get('User-Agent');

        return $record;
    }

    protected function generateRequestId(): string
    {
        return substr(uniqid('req_', true), 0, 20);
    }
}
