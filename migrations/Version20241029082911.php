<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Symfony\Component\Uid\Uuid;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241029082911 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE cargo_restriction (id UUID NOT NULL, cargo_type_id UUID NOT NULL, shipment_id UUID NOT NULL, max_width INT NOT NULL, max_height INT NOT NULL, max_length INT NOT NULL, max_weight INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_4F014FC1CD33D8BC ON cargo_restriction (cargo_type_id)');
        $this->addSql('CREATE INDEX IDX_4F014FC17BE036FC ON cargo_restriction (shipment_id)');
        $this->addSql('COMMENT ON COLUMN cargo_restriction.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN cargo_restriction.cargo_type_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN cargo_restriction.shipment_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE cargo_type (id UUID NOT NULL, code VARCHAR(50) NOT NULL, name VARCHAR(255) NOT NULL, is_active BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_cargo_type_name ON cargo_type (name) WHERE (is_active = true)');
        $this->addSql('CREATE INDEX idx_cargo_type_created_at ON cargo_type (created_at) WHERE (is_active = true)');
        $this->addSql('COMMENT ON COLUMN cargo_type.id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE cargo_restriction ADD CONSTRAINT FK_4F014FC1CD33D8BC FOREIGN KEY (cargo_type_id) REFERENCES cargo_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE cargo_restriction ADD CONSTRAINT FK_4F014FC17BE036FC FOREIGN KEY (shipment_id) REFERENCES shipment (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql(
            sprintf("INSERT INTO cargo_type (id, code, name, is_active, created_at) VALUES ('%s', 'small_sized', 'Малогабаритный груз', true, CURRENT_DATE)", Uuid::v1())
        );
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE cargo_restriction DROP CONSTRAINT FK_4F014FC1CD33D8BC');
        $this->addSql('ALTER TABLE cargo_restriction DROP CONSTRAINT FK_4F014FC17BE036FC');
        $this->addSql('DROP TABLE cargo_restriction');
        $this->addSql('DROP TABLE cargo_type');
    }
}
