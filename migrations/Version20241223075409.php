<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241223075409 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pickup_point ADD width NUMERIC(12, 2) DEFAULT NULL');
        $this->addSql('ALTER TABLE pickup_point ADD height NUMERIC(12, 2) DEFAULT NULL');
        $this->addSql('ALTER TABLE pickup_point ADD depth NUMERIC(12, 2) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE pickup_point DROP width');
        $this->addSql('ALTER TABLE pickup_point DROP height');
        $this->addSql('ALTER TABLE pickup_point DROP depth');
    }
}
