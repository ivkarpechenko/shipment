<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240606130300 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE store ADD psd DATE DEFAULT NULL');
        $this->addSql('ALTER TABLE store ADD psd_start_time TIME(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE store ADD psd_end_time TIME(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE store_schedule ALTER day TYPE SMALLINT');
        $this->addSql('ALTER TABLE store_schedule ALTER start_time TYPE VARCHAR(10)');
        $this->addSql('ALTER TABLE store_schedule ALTER start_time TYPE VARCHAR(10)');
        $this->addSql('ALTER TABLE store_schedule ALTER end_time TYPE VARCHAR(10)');
        $this->addSql('ALTER TABLE store_schedule ALTER end_time TYPE VARCHAR(10)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE store_schedule ALTER day TYPE SMALLINT');
        $this->addSql('ALTER TABLE store_schedule ALTER start_time TYPE VARCHAR(10)');
        $this->addSql('ALTER TABLE store_schedule ALTER end_time TYPE VARCHAR(10)');
        $this->addSql('ALTER TABLE store DROP psd');
        $this->addSql('ALTER TABLE store DROP psd_start_time');
        $this->addSql('ALTER TABLE store DROP psd_end_time');
    }
}
