<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240605133900 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE store_schedule (id UUID NOT NULL, store UUID DEFAULT NULL, day SMALLINT NOT NULL, start_time VARCHAR(10) NOT NULL, end_time VARCHAR(10) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_644F6E01FF575877 ON store_schedule (store)');
        $this->addSql('COMMENT ON TABLE store_schedule IS \'График работы склада\'');
        $this->addSql('COMMENT ON COLUMN store_schedule.id IS \'Уникальный идентификатор(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN store_schedule.store IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN store_schedule.day IS \'День недели\'');
        $this->addSql('COMMENT ON COLUMN store_schedule.start_time IS \'Начало рабочего дня.\'');
        $this->addSql('COMMENT ON COLUMN store_schedule.end_time IS \'Конец рабочего дня.\'');
        $this->addSql('ALTER TABLE store_schedule ADD CONSTRAINT FK_644F6E01FF575877 FOREIGN KEY (store) REFERENCES store (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE product ADD weight INT DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE product ADD volume INT DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE product ADD length INT DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE product ADD delivery_period INT DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE store DROP CONSTRAINT fk_ff5758774584665a');
        $this->addSql('DROP INDEX uniq_ff5758774584665a');
        $this->addSql('ALTER TABLE store ADD address UUID DEFAULT NULL');
        $this->addSql('ALTER TABLE store ADD external_store_id INT NOT NULL');
        $this->addSql('ALTER TABLE store RENAME COLUMN product_id TO contact');
        $this->addSql('COMMENT ON COLUMN store.address IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE store ADD CONSTRAINT FK_FF5758774C62E638 FOREIGN KEY (contact) REFERENCES contact (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE store ADD CONSTRAINT FK_FF575877D4E6F81 FOREIGN KEY (address) REFERENCES address (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_FF5758774C62E638 ON store (contact)');
        $this->addSql('CREATE INDEX IDX_FF575877D4E6F81 ON store (address)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE store_schedule DROP CONSTRAINT FK_644F6E01FF575877');
        $this->addSql('DROP TABLE store_schedule');
        $this->addSql('ALTER TABLE store DROP CONSTRAINT FK_FF5758774C62E638');
        $this->addSql('ALTER TABLE store DROP CONSTRAINT FK_FF575877D4E6F81');
        $this->addSql('DROP INDEX IDX_FF5758774C62E638');
        $this->addSql('DROP INDEX IDX_FF575877D4E6F81');
        $this->addSql('ALTER TABLE store ADD product_id UUID DEFAULT NULL');
        $this->addSql('ALTER TABLE store DROP contact');
        $this->addSql('ALTER TABLE store DROP address');
        $this->addSql('ALTER TABLE store DROP external_store_id');
        $this->addSql('COMMENT ON COLUMN store.product_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE store ADD CONSTRAINT fk_ff5758774584665a FOREIGN KEY (product_id) REFERENCES product (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX uniq_ff5758774584665a ON store (product_id)');
        $this->addSql('ALTER TABLE product DROP weight');
        $this->addSql('ALTER TABLE product DROP volume');
        $this->addSql('ALTER TABLE product DROP length');
        $this->addSql('ALTER TABLE product DROP delivery_period');
    }
}
