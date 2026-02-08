<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260208113057 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'create profiles table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
            CREATE TABLE IF NOT EXISTS profiles (
                id SERIAL PRIMARY KEY,
                user_id INT UNIQUE NOT NULL,
                name TEXT,
                email TEXT UNIQUE NOT NULL,
                is_email_verified BOOLEAN NOT NULL,
                created_at TIMESTAMP NOT NULL DEFAULT NOW(),
                synced_at TIMESTAMP NOT NULL DEFAULT NOW()
            );
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE IF EXISTS profiles;');
    }
}
