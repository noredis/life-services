<?php

declare(strict_types=1);

namespace App\Application\MessageHandler;

use App\Domain\Message\EmailVerificationAssignedEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Mime\Email;
use Throwable;

#[AsMessageHandler]
class SendVerificationEmailHandler
{
    public function __construct(
        private MailerInterface $mailer,
        private string $appUrl,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(EmailVerificationAssignedEvent $event): void
    {
        try {
            $url = $this->appUrl . '/api/account/email/verify/' . $event->verificationToken;

            $this->mailer->send(
                (new Email())
                    ->to($event->email)
                    ->from("noreply@example.com")
                    ->subject('Verify your email')
                    ->text("Please verify your email: {$url}"),
            );

            $this->logger->info('verification token sended', ['email' => $event->email]);
        } catch (Throwable $t) {
            $this->logger->error('failed to send email', ['error' => $t->getMessage()]);
        }
    }
}
