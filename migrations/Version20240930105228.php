<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Symfony\Component\Uid\Uuid;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240930105228 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE delivery_method (id UUID NOT NULL, code VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, is_active BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_delivery_method_name ON delivery_method (name) WHERE (is_active = true)');
        $this->addSql('CREATE INDEX idx_delivery_method_created_at ON delivery_method (created_at) WHERE (is_active = true)');
        $this->addSql('COMMENT ON COLUMN delivery_method.id IS \'(DC2Type:uuid)\'');

        $this->addSql(
            sprintf(
                "INSERT INTO delivery_method (id, code, name, is_active, created_at) VALUES ('%s','courier', 'Курьерская доставка', true, CURRENT_DATE)",
                (string) Uuid::v1()
            )
        );
        $this->addSql(
            sprintf(
                "INSERT INTO delivery_method (id, code, name, is_active, created_at) VALUES ('%s','pvz', 'ПВЗ', true, CURRENT_DATE)",
                (string) Uuid::v1()
            )
        );
        $this->addSql(
            sprintf(
                "INSERT INTO delivery_method (id, code, name, is_active, created_at) VALUES ('%s','pickup', 'Самовывоз', true, CURRENT_DATE)",
                (string) Uuid::v1()
            )
        );
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE delivery_method');
    }
}
