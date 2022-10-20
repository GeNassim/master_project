<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221020090500 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE clients ADD desabonne_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE clients ADD CONSTRAINT FK_C82E74495027C1 FOREIGN KEY (desabonne_id) REFERENCES desabonne (id)');
        $this->addSql('CREATE INDEX IDX_C82E74495027C1 ON clients (desabonne_id)');
        $this->addSql('ALTER TABLE desabonne DROP FOREIGN KEY FK_65FD8A3CAB014612');
        $this->addSql('DROP INDEX IDX_65FD8A3CAB014612 ON desabonne');
        $this->addSql('ALTER TABLE desabonne DROP clients_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE clients DROP FOREIGN KEY FK_C82E74495027C1');
        $this->addSql('DROP INDEX IDX_C82E74495027C1 ON clients');
        $this->addSql('ALTER TABLE clients DROP desabonne_id');
        $this->addSql('ALTER TABLE desabonne ADD clients_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE desabonne ADD CONSTRAINT FK_65FD8A3CAB014612 FOREIGN KEY (clients_id) REFERENCES clients (id)');
        $this->addSql('CREATE INDEX IDX_65FD8A3CAB014612 ON desabonne (clients_id)');
    }
}
