<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241128075547 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE okato_oktmo (id BIGINT NOT NULL, okato VARCHAR(255) NOT NULL, oktmo VARCHAR(255) NOT NULL, location VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_okato_oktmo_oktmo ON okato_oktmo (oktmo)');
        $this->addSql('CREATE UNIQUE INDEX uniq_okato_oktmo_okato ON okato_oktmo (okato)');
        $this->addSql('COMMENT ON TABLE okato_oktmo IS \'Таблица соответствия ОКАТО и ОКТМО\'');
        $this->addSql('COMMENT ON COLUMN okato_oktmo.id IS \'Первичный ключ\'');
        $this->addSql('COMMENT ON COLUMN okato_oktmo.okato IS \'Код ОКАТО\'');
        $this->addSql('COMMENT ON COLUMN okato_oktmo.oktmo IS \'Код ОКТМО\'');
        $this->addSql('COMMENT ON COLUMN okato_oktmo.location IS \'Наименование населенного пункта\'');
        $this->addSql('ALTER TABLE store_schedule ALTER day TYPE SMALLINT');
        $this->addSql('ALTER TABLE store_schedule ALTER start_time TYPE VARCHAR(10)');
        $this->addSql('ALTER TABLE store_schedule ALTER end_time TYPE VARCHAR(10)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE okato_oktmo');
        $this->addSql('ALTER TABLE store_schedule ALTER day TYPE SMALLINT');
        $this->addSql('ALTER TABLE store_schedule ALTER start_time TYPE VARCHAR(10)');
        $this->addSql('ALTER TABLE store_schedule ALTER end_time TYPE VARCHAR(10)');
    }
}
