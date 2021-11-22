<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211025121124 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE application ADD candidat_id INT NOT NULL');
        $this->addSql('ALTER TABLE application ADD CONSTRAINT FK_A45BDDC18D0EB82 FOREIGN KEY (candidat_id) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_A45BDDC18D0EB82 ON application (candidat_id)');
        $this->addSql('ALTER TABLE job ADD recruter_id INT NOT NULL');
        $this->addSql('ALTER TABLE job ADD CONSTRAINT FK_FBD8E0F8569B8235 FOREIGN KEY (recruter_id) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_FBD8E0F8569B8235 ON job (recruter_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE application DROP FOREIGN KEY FK_A45BDDC18D0EB82');
        $this->addSql('DROP INDEX IDX_A45BDDC18D0EB82 ON application');
        $this->addSql('ALTER TABLE application DROP candidat_id');
        $this->addSql('ALTER TABLE job DROP FOREIGN KEY FK_FBD8E0F8569B8235');
        $this->addSql('DROP INDEX IDX_FBD8E0F8569B8235 ON job');
        $this->addSql('ALTER TABLE job DROP recruter_id');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
