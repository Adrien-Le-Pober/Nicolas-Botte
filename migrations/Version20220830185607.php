<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220830185607 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE tva (id INT AUTO_INCREMENT NOT NULL, value DOUBLE PRECISION NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE atelier ADD tva_id INT NOT NULL');
        $this->addSql('ALTER TABLE atelier ADD CONSTRAINT FK_E1BB18234D79775F FOREIGN KEY (tva_id) REFERENCES tva (id)');
        $this->addSql('CREATE INDEX IDX_E1BB18234D79775F ON atelier (tva_id)');
        $this->addSql('ALTER TABLE `order` ADD tva DOUBLE PRECISION NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE atelier DROP FOREIGN KEY FK_E1BB18234D79775F');
        $this->addSql('DROP TABLE tva');
        $this->addSql('DROP INDEX IDX_E1BB18234D79775F ON atelier');
        $this->addSql('ALTER TABLE atelier DROP tva_id');
        $this->addSql('ALTER TABLE `order` DROP tva');
    }
}
