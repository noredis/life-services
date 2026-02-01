<?php

declare(strict_types=1);

namespace App\Application\Command;

use App\Domain\Entity\OutboxMessage;
use App\Domain\Interfaces\OutboxMessageRepositoryInterface;
use DateTimeImmutable;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\Option;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Throwable;

#[AsCommand(
    name: 'users:outbox:worker',
    description: 'sends messages from outbox_messages table to message bus'
)]
class OutboxWorkerCommand
{
    private bool $shouldStop = false;
    private ?DateTimeImmutable $startTime = null;

    public function __construct(
        private OutboxMessageRepositoryInterface $outboxMessageRepository,
        private MessageBusInterface $bus,
        private LoggerInterface $logger,
        private SerializerInterface $serializer,
    ) {
    }

    public function __invoke(
        OutputInterface $output,
        #[Option(name: 'limit', description: 'maximum number of messages to be processed in one cycle')]
        int $limit = 100,
        #[Option(name: 'sleep', description: 'waiting time between cycles in seconds')]
        int $sleep = 1,
        #[Option(name: 'time-limit', description: 'time in seconds after which worker will restart')]
        int $timeLimit = 3600,
    ): int {
        $this->startTime = new DateTimeImmutable();

        $this->setupSignalHandlers();

        $output->writeln('<info>Outbox worker is running.</info>');
        $output->writeln("<info>The worker will automatically exit once it has been running for {$timeLimit}s.</info>");
        $output->writeln('');
        $output->writeln('Quit the worker with CONTROL-C.');

        while (!$this->shouldStop) {
            try {
                if ($this->getWorkingTime() > $timeLimit) {
                    break;
                }

                $processedCount = $this->processMessages($limit);

                if ($processedCount > 0) {
                    $output->writeln("<comment>Messages processed: $processedCount</comment>");
                }

                if (!$this->shouldStop) {
                    sleep($sleep);
                }
            } catch (Throwable $e) {
                $this->logger->error('messages processing failed', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);

                $output->writeln("<error>Error received: {$e->getMessage()}</error>");

                sleep($sleep * 2);
            }
        }

        $output->writeln('<info>Outbox worker terminated.</info>');

        return Command::SUCCESS;
    }

    private function processMessages(int $limit): int
    {
        $messages = $this->outboxMessageRepository->getUnprocessedMessages($limit);
        if (empty($messages)) {
            return 0;
        }

        $processedCount = 0;
        foreach ($messages as $message) {
            if ($this->shouldStop) {
                break;
            }

            $this->processMessage($message);
            $processedCount++;
        }

        return $processedCount;
    }

    private function processMessage(OutboxMessage $message): void
    {
        try {
            $event = $this->serializer->deserialize(
                data: json_encode($message->getPayload()),
                type: $message->getType(),
                format: 'json',
            );

            $this->bus->dispatch($event);

            $this->outboxMessageRepository->markAsProcessed($message);

            $this->logger->info('outbox message processed', [
                'message_id' => $message->getId(),
                'event_type' => $message->getType(),
            ]);
        } catch (Throwable $t) {
            $this->logger->error('message processing failed', [
                'message_id' => $message->getId(),
                'error' => $t->getMessage(),
            ]);
        }
    }

    private function setupSignalHandlers(): void
    {
        if (!function_exists('pcntl_signal')) {
            return;
        }

        pcntl_signal(SIGTERM, function () {
            $this->shouldStop = true;
        });

        pcntl_signal(SIGINT, function () {
            $this->shouldStop = true;
        });

        pcntl_async_signals(true);
    }

    private function getWorkingTime(): int
    {
        $now = new DateTimeImmutable();
        return $now->getTimestamp() - $this->startTime->getTimestamp();
    }
}
