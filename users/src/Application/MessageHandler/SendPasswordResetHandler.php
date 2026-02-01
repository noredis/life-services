<?php

declare(strict_types=1);

namespace App\Application\MessageHandler;

use App\Domain\Message\PasswordResetAssignedEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Mime\Email;
use Throwable;

#[AsMessageHandler]
class SendPasswordResetHandler
{
    public function __construct(
        private MailerInterface $mailer,
        private string $appUrl,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(PasswordResetAssignedEvent $event): void
    {
        try {
            $url = $this->appUrl . '/api/account/password/reset/' . $event->resetToken;

            $this->mailer->send(
                (new Email())
                    ->to($event->email)
                    ->from("noreply@example.com")
                    ->subject('Confirm password reset')
                    ->text("Please confirm password reset: {$url}"),
            );

            $this->logger->info('password reset token sended', ['email' => $event->email]);
        } catch (Throwable $t) {
            $this->logger->error('failed to send email', ['error' => $t->getMessage()]);
        }
    }
}
