<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241211082810 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE delivery_method_delivery_service (delivery_method_id UUID NOT NULL, delivery_service_id UUID NOT NULL, PRIMARY KEY(delivery_method_id, delivery_service_id))');
        $this->addSql('CREATE INDEX IDX_D58FDB3F5DED75F5 ON delivery_method_delivery_service (delivery_method_id)');
        $this->addSql('CREATE INDEX IDX_D58FDB3FF3193EC2 ON delivery_method_delivery_service (delivery_service_id)');
        $this->addSql('COMMENT ON COLUMN delivery_method_delivery_service.delivery_method_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN delivery_method_delivery_service.delivery_service_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE delivery_method_delivery_service ADD CONSTRAINT FK_D58FDB3F5DED75F5 FOREIGN KEY (delivery_method_id) REFERENCES delivery_method (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE delivery_method_delivery_service ADD CONSTRAINT FK_D58FDB3FF3193EC2 FOREIGN KEY (delivery_service_id) REFERENCES delivery_service (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE delivery_method_delivery_service DROP CONSTRAINT FK_D58FDB3F5DED75F5');
        $this->addSql('ALTER TABLE delivery_method_delivery_service DROP CONSTRAINT FK_D58FDB3FF3193EC2');
        $this->addSql('DROP TABLE delivery_method_delivery_service');
    }
}
