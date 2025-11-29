<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241001105749 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE calculate ADD tariff_plan UUID DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN calculate.tariff_plan IS \'(DC2Type:uuid)\'');
        $this->addSql('UPDATE calculate SET tariff_plan = shipment.tariff_plan FROM shipment where calculate.shipment = shipment.id');
        $this->addSql('ALTER TABLE calculate ALTER COLUMN tariff_plan SET NOT NULL');
        $this->addSql('ALTER TABLE calculate ADD CONSTRAINT FK_228CFDB97A3EE6F3 FOREIGN KEY (tariff_plan) REFERENCES tariff_plan (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_228CFDB97A3EE6F3 ON calculate (tariff_plan)');
        $this->addSql('ALTER TABLE shipment DROP CONSTRAINT fk_2cb20dc7a3ee6f3');
        $this->addSql('DROP INDEX idx_2cb20dc7a3ee6f3');
        $this->addSql('ALTER TABLE shipment DROP tariff_plan');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE store_schedule ALTER day TYPE SMALLINT');
        $this->addSql('ALTER TABLE store_schedule ALTER start_time TYPE VARCHAR(10)');
        $this->addSql('ALTER TABLE store_schedule ALTER end_time TYPE VARCHAR(10)');
        $this->addSql('ALTER TABLE calculate DROP CONSTRAINT FK_228CFDB97A3EE6F3');
        $this->addSql('DROP INDEX IDX_228CFDB97A3EE6F3');
        $this->addSql('ALTER TABLE calculate DROP tariff_plan');
        $this->addSql('ALTER TABLE shipment ADD tariff_plan UUID DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN shipment.tariff_plan IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE shipment ADD CONSTRAINT fk_2cb20dc7a3ee6f3 FOREIGN KEY (tariff_plan) REFERENCES tariff_plan (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_2cb20dc7a3ee6f3 ON shipment (tariff_plan)');
    }
}
