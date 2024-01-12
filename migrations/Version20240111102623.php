<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240111102623 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user_chat_password MODIFY created_at DATETIME NULL');
        $this->addSql('ALTER TABLE user_chat_password MODIFY updated_at DATETIME NULL');

    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user_chat_password MODIFY created_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE user_chat_password MODIFY updated_at DATETIME NOT NULL');
    }
}
