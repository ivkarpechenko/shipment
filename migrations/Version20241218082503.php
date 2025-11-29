<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241218082503 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE calculate ALTER shipment SET NOT NULL');
        $this->addSql('ALTER TABLE pickup_point DROP CONSTRAINT fk_1467bee8f5b7af75');
        $this->addSql('DROP INDEX idx_pickup_point_address');
        $this->addSql('ALTER TABLE pickup_point ADD work_time VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE pickup_point ADD address VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE pickup_point ADD phones JSONB NOT NULL');
        $this->addSql('ALTER TABLE pickup_point ADD point POINT NOT NULL');
        $this->addSql('ALTER TABLE pickup_point DROP address_id');
        $this->addSql('COMMENT ON COLUMN pickup_point.point IS \'Широта и Долгота\'');
        $this->addSql('CREATE INDEX idx_pickup_point_address ON pickup_point (address)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP INDEX idx_pickup_point_address');
        $this->addSql('ALTER TABLE pickup_point ADD address_id UUID NOT NULL');
        $this->addSql('ALTER TABLE pickup_point DROP work_time');
        $this->addSql('ALTER TABLE pickup_point DROP address');
        $this->addSql('ALTER TABLE pickup_point DROP phones');
        $this->addSql('ALTER TABLE pickup_point DROP point');
        $this->addSql('COMMENT ON COLUMN pickup_point.address_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE pickup_point ADD CONSTRAINT fk_1467bee8f5b7af75 FOREIGN KEY (address_id) REFERENCES address (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_pickup_point_address ON pickup_point (address_id)');
        $this->addSql('ALTER TABLE calculate ALTER shipment DROP NOT NULL');
    }
}
