<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260208131520 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'add is_seller field to profiles table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
            ALTER TABLE profiles
            ADD COLUMN IF NOT EXISTS is_seller BOOLEAN NOT NULL DEFAULT FALSE;
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE profiles DROP COLUMN IF EXISTS is_seller;');
    }
}
