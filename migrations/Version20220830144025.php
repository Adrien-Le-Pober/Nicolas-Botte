<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220830144025 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE atelier DROP FOREIGN KEY FK_E1BB1823ED5CA9E6');
        $this->addSql('ALTER TABLE atelier CHANGE service_id service_id INT NOT NULL');
        $this->addSql('ALTER TABLE atelier ADD CONSTRAINT FK_E1BB1823ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tarif DROP FOREIGN KEY FK_E7189C9ED5CA9E6');
        $this->addSql('ALTER TABLE tarif ADD CONSTRAINT FK_E7189C9ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE atelier DROP FOREIGN KEY FK_E1BB1823ED5CA9E6');
        $this->addSql('ALTER TABLE atelier CHANGE service_id service_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE atelier ADD CONSTRAINT FK_E1BB1823ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE tarif DROP FOREIGN KEY FK_E7189C9ED5CA9E6');
        $this->addSql('ALTER TABLE tarif ADD CONSTRAINT FK_E7189C9ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }
}
