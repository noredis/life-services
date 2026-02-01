<?php

declare(strict_types=1);

namespace App\Common\Logger;

use Monolog\LogRecord;
use Monolog\Processor\ProcessorInterface;
use Symfony\Bundle\SecurityBundle\Security;

class UserIdentifiedProcessor implements ProcessorInterface
{
    public function __construct(
        private Security $security,
    ) {
    }

    public function __invoke(LogRecord $record): LogRecord
    {
        $user = $this->security->getUser();
        if ($user !== null) {
            $record->extra['user_email'] = $user->getUserIdentifier();
        }

        return $record;
    }
}
