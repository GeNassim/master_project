<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221012110809 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE envois ADD client_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE envois ADD CONSTRAINT FK_BF15755319EB6921 FOREIGN KEY (client_id) REFERENCES clients (id)');
        $this->addSql('CREATE INDEX IDX_BF15755319EB6921 ON envois (client_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE envois DROP FOREIGN KEY FK_BF15755319EB6921');
        $this->addSql('DROP INDEX IDX_BF15755319EB6921 ON envois');
        $this->addSql('ALTER TABLE envois DROP client_id');
    }
}
