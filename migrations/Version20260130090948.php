<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260130090948 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'create outbox_messages table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
            CREATE TABLE IF NOT EXISTS outbox_messages (
                id SERIAL PRIMARY KEY,
                type TEXT NOT NULL,
                payload JSONB NOT NULL,
                created_at TIMESTAMP NOT NULL,
                processed_at TIMESTAMP DEFAULT NULL
            );
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('
            DROP TABLE IF EXISTS outbox_messages;
        ');
    }
}
