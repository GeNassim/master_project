<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221002165024 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE contact ADD clients_id INT DEFAULT NULL, ADD created_at DATE NOT NULL, DROP inscritaujourdhui, DROP inscrithier, DROP totalinscrit, DROP totaldesinscrit');
        $this->addSql('ALTER TABLE contact ADD CONSTRAINT FK_4C62E638AB014612 FOREIGN KEY (clients_id) REFERENCES clients (id)');
        $this->addSql('CREATE INDEX IDX_4C62E638AB014612 ON contact (clients_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE contact DROP FOREIGN KEY FK_4C62E638AB014612');
        $this->addSql('DROP INDEX IDX_4C62E638AB014612 ON contact');
        $this->addSql('ALTER TABLE contact ADD inscrithier INT DEFAULT NULL, ADD totalinscrit INT DEFAULT NULL, ADD totaldesinscrit INT DEFAULT NULL, DROP created_at, CHANGE clients_id inscritaujourdhui INT DEFAULT NULL');
    }
}
