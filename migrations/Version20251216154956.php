<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251216154956 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE chambre ADD chambre_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE chambre ADD CONSTRAINT FK_C509E4FF9B177F54 FOREIGN KEY (chambre_id) REFERENCES service (id)');
        $this->addSql('CREATE INDEX IDX_C509E4FF9B177F54 ON chambre (chambre_id)');
        $this->addSql('ALTER TABLE sejour DROP FOREIGN KEY FK_96F5202848D62931');
        $this->addSql('DROP INDEX IDX_96F5202848D62931 ON sejour');
        $this->addSql('ALTER TABLE sejour DROP id_service');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE chambre DROP FOREIGN KEY FK_C509E4FF9B177F54');
        $this->addSql('DROP INDEX IDX_C509E4FF9B177F54 ON chambre');
        $this->addSql('ALTER TABLE chambre DROP chambre_id');
        $this->addSql('ALTER TABLE sejour ADD id_service INT DEFAULT NULL');
        $this->addSql('ALTER TABLE sejour ADD CONSTRAINT FK_96F5202848D62931 FOREIGN KEY (id_service) REFERENCES service (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_96F5202848D62931 ON sejour (id_service)');
    }
}
