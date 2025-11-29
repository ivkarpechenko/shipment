<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240531113539 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE store (id UUID NOT NULL, product_id UUID DEFAULT NULL, max_weight INT NOT NULL, max_volume INT NOT NULL, max_length INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_FF5758774584665A ON store (product_id)');
        $this->addSql('COMMENT ON COLUMN store.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN store.product_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE store ADD CONSTRAINT FK_FF5758774584665A FOREIGN KEY (product_id) REFERENCES product (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE product ADD store_id UUID DEFAULT NULL');
        $this->addSql('ALTER TABLE product ADD fragility BOOLEAN DEFAULT false NOT NULL');
        $this->addSql('COMMENT ON COLUMN product.store_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04ADB092A811 FOREIGN KEY (store_id) REFERENCES store (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D34A04ADB092A811 ON product (store_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product DROP CONSTRAINT FK_D34A04ADB092A811');
        $this->addSql('ALTER TABLE store DROP CONSTRAINT FK_FF5758774584665A');
        $this->addSql('DROP TABLE store');
        $this->addSql('DROP INDEX UNIQ_D34A04ADB092A811');
        $this->addSql('ALTER TABLE product DROP store_id');
        $this->addSql('ALTER TABLE product DROP fragility');
    }
}
