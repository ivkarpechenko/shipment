<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230606093954 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE package ADD price NUMERIC(12, 2) NOT NULL');
        $this->addSql('ALTER TABLE shipment ADD psd_start_time TIME(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('ALTER TABLE shipment ADD psd_end_time TIME(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('ALTER TABLE shipment ALTER psd TYPE DATE');
        $this->addSql('ALTER TABLE shipment ALTER psd SET NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE package DROP price');
        $this->addSql('ALTER TABLE shipment DROP psd_start_time');
        $this->addSql('ALTER TABLE shipment DROP psd_end_time');
        $this->addSql('ALTER TABLE shipment ALTER psd TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE shipment ALTER psd DROP NOT NULL');
    }
}
