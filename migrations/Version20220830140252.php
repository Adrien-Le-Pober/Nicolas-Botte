<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220830140252 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE atelier CHANGE short_description short_description VARCHAR(160) DEFAULT NULL');
        $this->addSql('DROP INDEX reference ON `order`');
        $this->addSql('ALTER TABLE tarif DROP FOREIGN KEY FK_E7189C9ED5CA9E6');
        $this->addSql('ALTER TABLE tarif ADD CONSTRAINT FK_E7189C9ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE atelier CHANGE short_description short_description VARCHAR(500) DEFAULT NULL');
        $this->addSql('CREATE FULLTEXT INDEX reference ON `order` (reference)');
        $this->addSql('ALTER TABLE tarif DROP FOREIGN KEY FK_E7189C9ED5CA9E6');
        $this->addSql('ALTER TABLE tarif ADD CONSTRAINT FK_E7189C9ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id) ON UPDATE NO ACTION ON DELETE CASCADE');
    }
}
