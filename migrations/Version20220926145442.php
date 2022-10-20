<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220926145442 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE atterissage (id INT AUTO_INCREMENT NOT NULL, tunnel_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, url VARCHAR(255) NOT NULL, visuel VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, INDEX IDX_FDBE22ADFFA3ECB5 (tunnel_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE atterissage ADD CONSTRAINT FK_FDBE22ADFFA3ECB5 FOREIGN KEY (tunnel_id) REFERENCES tunnel (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE atterissage DROP FOREIGN KEY FK_FDBE22ADFFA3ECB5');
        $this->addSql('DROP TABLE atterissage');
    }
}
