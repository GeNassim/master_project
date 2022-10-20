<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221019205433 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE clients ADD is_verfied TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE etape DROP date, DROP heure, DROP fichier');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE clients DROP is_verfied');
        $this->addSql('ALTER TABLE etape ADD date DATE DEFAULT NULL, ADD heure TIME DEFAULT NULL, ADD fichier VARCHAR(255) DEFAULT NULL');
    }
}
