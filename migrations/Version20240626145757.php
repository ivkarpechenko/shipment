<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240626145757 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX uniq_d34a04adb092a811');
        $this->addSql('ALTER TABLE product ADD height INT DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE product ADD is_flammable BOOLEAN DEFAULT false NOT NULL');
        $this->addSql('ALTER TABLE product ADD is_can_rotate BOOLEAN DEFAULT false NOT NULL');
        $this->addSql('ALTER TABLE product ALTER weight TYPE DOUBLE PRECISION');
        $this->addSql('ALTER TABLE product RENAME COLUMN volume TO width');
        $this->addSql('ALTER TABLE product RENAME COLUMN fragility TO is_fragile');
        $this->addSql('CREATE INDEX IDX_D34A04ADB092A811 ON product (store_id)');
        $this->addSql('ALTER TABLE store ALTER is_pickup SET DEFAULT false');
        $this->addSql('ALTER TABLE store RENAME COLUMN external_store_id TO external_id');
        $this->addSql('ALTER TABLE store_schedule ALTER day TYPE SMALLINT');
        $this->addSql('ALTER TABLE store_schedule ALTER start_time TYPE VARCHAR(10)');
        $this->addSql('ALTER TABLE store_schedule ALTER start_time TYPE VARCHAR(10)');
        $this->addSql('ALTER TABLE store_schedule ALTER end_time TYPE VARCHAR(10)');
        $this->addSql('ALTER TABLE store_schedule ALTER end_time TYPE VARCHAR(10)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE store_schedule ALTER day TYPE SMALLINT');
        $this->addSql('ALTER TABLE store_schedule ALTER start_time TYPE VARCHAR(10)');
        $this->addSql('ALTER TABLE store_schedule ALTER end_time TYPE VARCHAR(10)');
        $this->addSql('ALTER TABLE store ALTER is_pickup SET DEFAULT true');
        $this->addSql('ALTER TABLE store RENAME COLUMN external_id TO external_store_id');
        $this->addSql('DROP INDEX IDX_D34A04ADB092A811');
        $this->addSql('ALTER TABLE product ADD fragility BOOLEAN DEFAULT false NOT NULL');
        $this->addSql('ALTER TABLE product ADD volume INT DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE product DROP width');
        $this->addSql('ALTER TABLE product DROP height');
        $this->addSql('ALTER TABLE product DROP is_fragile');
        $this->addSql('ALTER TABLE product DROP is_flammable');
        $this->addSql('ALTER TABLE product DROP is_can_rotate');
        $this->addSql('ALTER TABLE product ALTER weight TYPE INT');
        $this->addSql('CREATE UNIQUE INDEX uniq_d34a04adb092a811 ON product (store_id)');
    }
}
