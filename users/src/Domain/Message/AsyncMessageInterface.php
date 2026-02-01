<?php

declare(strict_types=1);

namespace App\Domain\Message;

interface AsyncMessageInterface
{
    public function getPayload(): array;
}
