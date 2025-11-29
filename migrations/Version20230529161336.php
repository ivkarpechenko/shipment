<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230529161336 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE calculate (id UUID NOT NULL, shipment UUID DEFAULT NULL, min_period INT NOT NULL, max_period INT NOT NULL, delivery_cost DOUBLE PRECISION NOT NULL, delivery_total_cost DOUBLE PRECISION NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, expired_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_228CFDB92CB20DC ON calculate (shipment)');
        $this->addSql('CREATE INDEX idx_calculate_created_at ON calculate (created_at)');
        $this->addSql('CREATE INDEX idx_calculate_expired_at ON calculate (expired_at)');
        $this->addSql('COMMENT ON COLUMN calculate.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN calculate.shipment IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE calculate ADD CONSTRAINT FK_228CFDB92CB20DC FOREIGN KEY (shipment) REFERENCES shipment (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE calculate DROP CONSTRAINT FK_228CFDB92CB20DC');
        $this->addSql('DROP TABLE calculate');
    }
}
