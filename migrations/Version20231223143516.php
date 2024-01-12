<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231223143516 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('INSERT INTO language (name) VALUES (\'english\')');
        $this->addSql('INSERT INTO language (name) VALUES (\'french\')');
        $this->addSql('INSERT INTO language (name) VALUES (\'italian\')');
        $this->addSql('INSERT INTO language (name) VALUES (\'german\')');
        $this->addSql('INSERT INTO language (name) VALUES (\'arab\')');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DELETE FROM language WHERE name = \'english\'');
        $this->addSql('DELETE FROM language WHERE name = \'french\'');
        $this->addSql('DELETE FROM language WHERE name = \'italian\'');
        $this->addSql('DELETE FROM language WHERE name = \'german\'');
        $this->addSql('DELETE FROM language WHERE name = \'arab\'');
    }
}
