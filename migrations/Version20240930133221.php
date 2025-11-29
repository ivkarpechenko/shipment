<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240930133221 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE store_schedule ALTER day TYPE SMALLINT');
        $this->addSql('ALTER TABLE store_schedule ALTER start_time TYPE VARCHAR(10)');
        $this->addSql('ALTER TABLE store_schedule ALTER start_time TYPE VARCHAR(10)');
        $this->addSql('ALTER TABLE store_schedule ALTER end_time TYPE VARCHAR(10)');
        $this->addSql('ALTER TABLE store_schedule ALTER end_time TYPE VARCHAR(10)');
        $this->addSql('DROP INDEX tariff_plan_unique_code');
        $this->addSql('ALTER TABLE tariff_plan ADD delivery_method_id UUID');
        $this->addSql('COMMENT ON COLUMN tariff_plan.delivery_method_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE tariff_plan ADD CONSTRAINT FK_7A3EE6F35DED75F5 FOREIGN KEY (delivery_method_id) REFERENCES delivery_method (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql("UPDATE tariff_plan SET delivery_method_id = (SELECT id FROM delivery_method where code='courier')");
        $this->addSql('CREATE INDEX IDX_7A3EE6F35DED75F5 ON tariff_plan (delivery_method_id)');
        $this->addSql('CREATE UNIQUE INDEX tariff_plan_unique_code ON tariff_plan (delivery_service_id, delivery_method_id, code)');
        $this->addSql('ALTER TABLE tariff_plan ALTER COLUMN delivery_method_id SET NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE tariff_plan DROP CONSTRAINT FK_7A3EE6F35DED75F5');
        $this->addSql('DROP INDEX IDX_7A3EE6F35DED75F5');
        $this->addSql('DROP INDEX tariff_plan_unique_code');
        $this->addSql('ALTER TABLE tariff_plan DROP delivery_method_id');
        $this->addSql('CREATE UNIQUE INDEX tariff_plan_unique_code ON tariff_plan (delivery_service_id, code)');
        $this->addSql('ALTER TABLE store_schedule ALTER day TYPE SMALLINT');
        $this->addSql('ALTER TABLE store_schedule ALTER start_time TYPE VARCHAR(10)');
        $this->addSql('ALTER TABLE store_schedule ALTER end_time TYPE VARCHAR(10)');
    }
}
