<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240410085211 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE EXTENSION IF NOT EXISTS postgis;');
        $this->addSql('CREATE TABLE delivery_service_restrict_area (id UUID NOT NULL, delivery_service UUID DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, polygon GEOMETRY NOT NULL, is_active BOOLEAN DEFAULT true NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_B279C651EB83250C ON delivery_service_restrict_area (delivery_service)');
        $this->addSql('CREATE INDEX idx_delivery_service_restrict_area_created_at ON delivery_service_restrict_area (created_at) WHERE (is_active = true)');
        $this->addSql('COMMENT ON COLUMN delivery_service_restrict_area.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN delivery_service_restrict_area.delivery_service IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE delivery_service_restrict_area ADD CONSTRAINT FK_B279C651EB83250C FOREIGN KEY (delivery_service) REFERENCES delivery_service (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE delivery_service_restrict_area DROP CONSTRAINT FK_B279C651EB83250C');
        $this->addSql('DROP TABLE delivery_service_restrict_area');
    }
}
