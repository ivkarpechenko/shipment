<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241010101542 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE delivery_service_restrict_package (id UUID NOT NULL, delivery_service_id UUID NOT NULL, max_weight INT NOT NULL, max_width INT NOT NULL, max_height INT NOT NULL, max_length INT NOT NULL, is_active BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX uidx_delivery_service_restrict_package_delivery_service_id ON delivery_service_restrict_package (delivery_service_id)');
        $this->addSql('COMMENT ON COLUMN delivery_service_restrict_package.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN delivery_service_restrict_package.delivery_service_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE delivery_service_restrict_package ADD CONSTRAINT FK_DC211F5CF3193EC2 FOREIGN KEY (delivery_service_id) REFERENCES delivery_service (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE delivery_service_restrict_package DROP CONSTRAINT FK_DC211F5CF3193EC2');
        $this->addSql('DROP TABLE delivery_service_restrict_package');
    }
}
