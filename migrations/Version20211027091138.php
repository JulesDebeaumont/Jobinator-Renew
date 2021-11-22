<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211027091138 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE job ADD departement VARCHAR(5) DEFAULT NULL, CHANGE start start DATE DEFAULT NULL, CHANGE location location VARCHAR(255) DEFAULT NULL, CHANGE is_remote is_remote TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE job DROP departement, CHANGE start start DATE NOT NULL, CHANGE location location VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE is_remote is_remote TINYINT(1) DEFAULT NULL');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
