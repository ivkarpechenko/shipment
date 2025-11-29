<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230526091203 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE address (id UUID NOT NULL, city_id UUID NOT NULL, address VARCHAR(500) NOT NULL, postal_code VARCHAR(50) NOT NULL, street VARCHAR(50) NOT NULL, house VARCHAR(50) DEFAULT NULL, flat VARCHAR(50) DEFAULT NULL, entrance VARCHAR(50) DEFAULT NULL, floor VARCHAR(50) DEFAULT NULL, is_active BOOLEAN DEFAULT true NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D4E6F81D4E6F81 ON address (address)');
        $this->addSql('CREATE INDEX IDX_D4E6F818BAC62AF ON address (city_id)');
        $this->addSql('CREATE INDEX idx_address_created_at ON address (created_at) WHERE (deleted_at IS NULL)');
        $this->addSql('COMMENT ON COLUMN address.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN address.city_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE city (id UUID NOT NULL, region_id UUID NOT NULL, name VARCHAR(255) NOT NULL, type VARCHAR(50) NOT NULL, is_active BOOLEAN DEFAULT true NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_2D5B023498260155 ON city (region_id)');
        $this->addSql('CREATE INDEX idx_city_created_at ON city (created_at) WHERE (deleted_at IS NULL)');
        $this->addSql('CREATE UNIQUE INDEX city_unique_region_id_type_name ON city (region_id, type, name)');
        $this->addSql('COMMENT ON COLUMN city.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN city.region_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE contact (id UUID NOT NULL, email VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_4C62E638E7927C74 ON contact (email)');
        $this->addSql('CREATE INDEX idx_contact_created_at ON contact (created_at)');
        $this->addSql('COMMENT ON COLUMN contact.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE country (id UUID NOT NULL, name VARCHAR(250) NOT NULL, code VARCHAR(2) NOT NULL, is_active BOOLEAN DEFAULT true NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5373C9665E237E06 ON country (name)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5373C96677153098 ON country (code)');
        $this->addSql('CREATE INDEX idx_country_created_at ON country (created_at) WHERE (deleted_at IS NULL)');
        $this->addSql('COMMENT ON COLUMN country.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE currency (id UUID NOT NULL, code VARCHAR(3) NOT NULL, num INT NOT NULL, name VARCHAR(100) NOT NULL, is_active BOOLEAN DEFAULT true NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6956883F77153098 ON currency (code)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6956883FDC43AF6E ON currency (num)');
        $this->addSql('CREATE INDEX idx_currency_name ON currency (name) WHERE (is_active = true)');
        $this->addSql('CREATE INDEX idx_currency_created_at ON currency (created_at) WHERE (is_active = true)');
        $this->addSql('COMMENT ON COLUMN currency.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE delivery_service (id UUID NOT NULL, code VARCHAR(50) NOT NULL, name VARCHAR(250) NOT NULL, is_active BOOLEAN DEFAULT true NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_EB83250C77153098 ON delivery_service (code)');
        $this->addSql('CREATE INDEX idx_delivery_service_name ON delivery_service (name) WHERE (is_active = true)');
        $this->addSql('CREATE INDEX idx_delivery_service_created_at ON delivery_service (created_at) WHERE (is_active = true)');
        $this->addSql('COMMENT ON COLUMN delivery_service.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE package (id UUID NOT NULL, shipment_id UUID DEFAULT NULL, width INT NOT NULL, height INT NOT NULL, length INT NOT NULL, weight INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_DE6867957BE036FC ON package (shipment_id)');
        $this->addSql('COMMENT ON COLUMN package.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN package.shipment_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE phone (id UUID NOT NULL, contact_id UUID DEFAULT NULL, number VARCHAR(15) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_444F97DDE7A1254A ON phone (contact_id)');
        $this->addSql('COMMENT ON COLUMN phone.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN phone.contact_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE region (id UUID NOT NULL, country_id UUID NOT NULL, name VARCHAR(255) NOT NULL, code VARCHAR(7) NOT NULL, is_active BOOLEAN DEFAULT true NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F62F1765E237E06 ON region (name)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F62F17677153098 ON region (code)');
        $this->addSql('CREATE INDEX IDX_F62F176F92F3E70 ON region (country_id)');
        $this->addSql('CREATE INDEX idx_region_created_at ON region (created_at) WHERE (deleted_at IS NULL)');
        $this->addSql('COMMENT ON COLUMN region.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN region.country_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE shipment (id UUID NOT NULL, sender UUID DEFAULT NULL, recipient UUID DEFAULT NULL, tariff_plan UUID DEFAULT NULL, currency UUID DEFAULT NULL, psd TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, "from" UUID DEFAULT NULL, "to" UUID DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_2CB20DC6A5A3777 ON shipment ("from")');
        $this->addSql('CREATE INDEX IDX_2CB20DC4F824EA9 ON shipment ("to")');
        $this->addSql('CREATE INDEX IDX_2CB20DC5F004ACF ON shipment (sender)');
        $this->addSql('CREATE INDEX IDX_2CB20DC6804FB49 ON shipment (recipient)');
        $this->addSql('CREATE INDEX IDX_2CB20DC7A3EE6F3 ON shipment (tariff_plan)');
        $this->addSql('CREATE INDEX IDX_2CB20DC6956883F ON shipment (currency)');
        $this->addSql('CREATE INDEX idx_shipment_created_at ON shipment (created_at)');
        $this->addSql('COMMENT ON COLUMN shipment.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN shipment.sender IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN shipment.recipient IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN shipment.tariff_plan IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN shipment.currency IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN shipment."from" IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN shipment."to" IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE tariff_plan (id UUID NOT NULL, delivery_service_id UUID NOT NULL, code VARCHAR(100) NOT NULL, name VARCHAR(255) NOT NULL, is_active BOOLEAN DEFAULT true NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_7A3EE6F3F3193EC2 ON tariff_plan (delivery_service_id)');
        $this->addSql('CREATE INDEX idx_tariff_plan_name ON tariff_plan (name) WHERE (is_active = true)');
        $this->addSql('CREATE INDEX idx_tariff_plan_created_at ON tariff_plan (created_at) WHERE (is_active = true)');
        $this->addSql('CREATE UNIQUE INDEX tariff_plan_unique_code ON tariff_plan (delivery_service_id, code)');
        $this->addSql('COMMENT ON COLUMN tariff_plan.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN tariff_plan.delivery_service_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE messenger_messages (id BIGSERIAL NOT NULL, body TEXT NOT NULL, headers TEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
        $this->addSql('COMMENT ON COLUMN messenger_messages.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_messages.available_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_messages.delivered_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE OR REPLACE FUNCTION notify_messenger_messages() RETURNS TRIGGER AS $$
            BEGIN
                PERFORM pg_notify(\'messenger_messages\', NEW.queue_name::text);
                RETURN NEW;
            END;
        $$ LANGUAGE plpgsql;');
        $this->addSql('DROP TRIGGER IF EXISTS notify_trigger ON messenger_messages;');
        $this->addSql('CREATE TRIGGER notify_trigger AFTER INSERT OR UPDATE ON messenger_messages FOR EACH ROW EXECUTE PROCEDURE notify_messenger_messages();');
        $this->addSql('ALTER TABLE address ADD CONSTRAINT FK_D4E6F818BAC62AF FOREIGN KEY (city_id) REFERENCES city (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE city ADD CONSTRAINT FK_2D5B023498260155 FOREIGN KEY (region_id) REFERENCES region (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE package ADD CONSTRAINT FK_DE6867957BE036FC FOREIGN KEY (shipment_id) REFERENCES shipment (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE phone ADD CONSTRAINT FK_444F97DDE7A1254A FOREIGN KEY (contact_id) REFERENCES contact (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE region ADD CONSTRAINT FK_F62F176F92F3E70 FOREIGN KEY (country_id) REFERENCES country (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE shipment ADD CONSTRAINT FK_2CB20DC6A5A3777 FOREIGN KEY ("from") REFERENCES address ("id") NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE shipment ADD CONSTRAINT FK_2CB20DC4F824EA9 FOREIGN KEY ("to") REFERENCES address ("id") NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE shipment ADD CONSTRAINT FK_2CB20DC5F004ACF FOREIGN KEY (sender) REFERENCES contact (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE shipment ADD CONSTRAINT FK_2CB20DC6804FB49 FOREIGN KEY (recipient) REFERENCES contact (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE shipment ADD CONSTRAINT FK_2CB20DC7A3EE6F3 FOREIGN KEY (tariff_plan) REFERENCES tariff_plan (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE shipment ADD CONSTRAINT FK_2CB20DC6956883F FOREIGN KEY (currency) REFERENCES currency (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tariff_plan ADD CONSTRAINT FK_7A3EE6F3F3193EC2 FOREIGN KEY (delivery_service_id) REFERENCES delivery_service (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE address DROP CONSTRAINT FK_D4E6F818BAC62AF');
        $this->addSql('ALTER TABLE city DROP CONSTRAINT FK_2D5B023498260155');
        $this->addSql('ALTER TABLE package DROP CONSTRAINT FK_DE6867957BE036FC');
        $this->addSql('ALTER TABLE phone DROP CONSTRAINT FK_444F97DDE7A1254A');
        $this->addSql('ALTER TABLE region DROP CONSTRAINT FK_F62F176F92F3E70');
        $this->addSql('ALTER TABLE shipment DROP CONSTRAINT FK_2CB20DC6A5A3777');
        $this->addSql('ALTER TABLE shipment DROP CONSTRAINT FK_2CB20DC4F824EA9');
        $this->addSql('ALTER TABLE shipment DROP CONSTRAINT FK_2CB20DC5F004ACF');
        $this->addSql('ALTER TABLE shipment DROP CONSTRAINT FK_2CB20DC6804FB49');
        $this->addSql('ALTER TABLE shipment DROP CONSTRAINT FK_2CB20DC7A3EE6F3');
        $this->addSql('ALTER TABLE shipment DROP CONSTRAINT FK_2CB20DC6956883F');
        $this->addSql('ALTER TABLE tariff_plan DROP CONSTRAINT FK_7A3EE6F3F3193EC2');
        $this->addSql('DROP TABLE address');
        $this->addSql('DROP TABLE city');
        $this->addSql('DROP TABLE contact');
        $this->addSql('DROP TABLE country');
        $this->addSql('DROP TABLE currency');
        $this->addSql('DROP TABLE delivery_service');
        $this->addSql('DROP TABLE package');
        $this->addSql('DROP TABLE phone');
        $this->addSql('DROP TABLE region');
        $this->addSql('DROP TABLE shipment');
        $this->addSql('DROP TABLE tariff_plan');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
