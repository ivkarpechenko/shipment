<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250120072504 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE shipment ADD "pickup_point" UUID DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN shipment."pickup_point" IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE shipment ADD CONSTRAINT FK_2CB20DC5B6220AD FOREIGN KEY ("pickup_point") REFERENCES pickup_point ("id") NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_2CB20DC5B6220AD ON shipment ("pickup_point")');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE shipment DROP CONSTRAINT FK_2CB20DC5B6220AD');
        $this->addSql('DROP INDEX IDX_2CB20DC5B6220AD');
        $this->addSql('ALTER TABLE shipment DROP "pickup_point"');
    }
}
