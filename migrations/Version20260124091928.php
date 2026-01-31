<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260124091928 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'create users table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
            CREATE TABLE IF NOT EXISTS email_verifications (
                id SERIAL PRIMARY KEY,
                token TEXT NOT NULL,
                expires_in TIMESTAMP NOT NULL,
                created_at TIMESTAMP NOT NULL DEFAULT NOW()
            );
        ');
        $this->addSql('
            CREATE TABLE IF NOT EXISTS password_resets (
                id SERIAL PRIMARY KEY,
                token TEXT NOT NULL,
                expires_in TIMESTAMP NOT NULL,
                created_at TIMESTAMP NOT NULL DEFAULT NOW()
            );
        ');
        $this->addSql('
            CREATE TABLE IF NOT EXISTS users (
                id SERIAL PRIMARY KEY,
                email_verification_id INT UNIQUE NOT NULL,
                password_reset_id INT UNIQUE,
                name TEXT,
                email TEXT UNIQUE NOT NULL,
                password TEXT NOT NULL,
                created_at TIMESTAMP NOT NULL DEFAULT NOW(),
                updated_at TIMESTAMP NOT NULL DEFAULT NOW(),
                password_updated_at TIMESTAMP NOT NULL DEFAULT NOW(),
                email_verified_at TIMESTAMP,
                CONSTRAINT fk_email_verification
                    FOREIGN KEY (email_verification_id)
                    REFERENCES email_verifications(id)
            );
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE IF EXISTS users;');
        $this->addSql('DROP TABLE IF EXISTS email_verifications;');
        $this->addSql('DROP TABLE IF EXISTS password_resets');
    }
}
