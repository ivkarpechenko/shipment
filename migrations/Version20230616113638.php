<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230616113638 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE tax (id UUID NOT NULL, country_id UUID NOT NULL, name VARCHAR(250) NOT NULL, value DOUBLE PRECISION NOT NULL, expression VARCHAR(250) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_8E81BA76F92F3E70 ON tax (country_id)');
        $this->addSql('CREATE UNIQUE INDEX country_id_name ON tax (country_id, name)');
        $this->addSql('COMMENT ON COLUMN tax.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN tax.country_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE tax ADD CONSTRAINT FK_8E81BA76F92F3E70 FOREIGN KEY (country_id) REFERENCES country (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE calculate ADD delivery_total_cost_tax DOUBLE PRECISION DEFAULT \'0\' NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE tax DROP CONSTRAINT FK_8E81BA76F92F3E70');
        $this->addSql('DROP TABLE tax');
        $this->addSql('ALTER TABLE calculate DROP delivery_total_cost_tax');
    }
}
