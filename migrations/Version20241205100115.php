<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241205100115 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE pickup_point (id UUID NOT NULL, address_id UUID NOT NULL, delivery_service_id UUID NOT NULL, code VARCHAR(100) NOT NULL, type VARCHAR(100) NOT NULL, weight_min NUMERIC(12, 2) DEFAULT NULL, weight_max NUMERIC(12, 2) DEFAULT NULL, is_active BOOLEAN DEFAULT true NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_1467BEE8F3193EC2 ON pickup_point (delivery_service_id)');
        $this->addSql('CREATE INDEX idx_pickup_point_address ON pickup_point (address_id)');
        $this->addSql('CREATE UNIQUE INDEX pickup_point_unique_delivery_service_id_code ON pickup_point (delivery_service_id, code)');
        $this->addSql('COMMENT ON COLUMN pickup_point.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN pickup_point.address_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN pickup_point.delivery_service_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE pickup_point ADD CONSTRAINT FK_1467BEE8F5B7AF75 FOREIGN KEY (address_id) REFERENCES address (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE pickup_point ADD CONSTRAINT FK_1467BEE8F3193EC2 FOREIGN KEY (delivery_service_id) REFERENCES delivery_service (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE pickup_point DROP CONSTRAINT FK_1467BEE8F5B7AF75');
        $this->addSql('ALTER TABLE pickup_point DROP CONSTRAINT FK_1467BEE8F3193EC2');
        $this->addSql('DROP TABLE pickup_point');
    }
}
