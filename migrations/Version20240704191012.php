<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240704191012 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE package_product (package_id UUID NOT NULL, product_id UUID NOT NULL, PRIMARY KEY(package_id, product_id))');
        $this->addSql('CREATE INDEX IDX_5C116121F44CABFF ON package_product (package_id)');
        $this->addSql('CREATE INDEX IDX_5C1161214584665A ON package_product (product_id)');
        $this->addSql('COMMENT ON COLUMN package_product.package_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN package_product.product_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE package_product ADD CONSTRAINT FK_5C116121F44CABFF FOREIGN KEY (package_id) REFERENCES package (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE package_product ADD CONSTRAINT FK_5C1161214584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE product DROP CONSTRAINT fk_d34a04adf44cabff');
        $this->addSql('DROP INDEX idx_d34a04adf44cabff');
        $this->addSql('ALTER TABLE product DROP package_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE package_product DROP CONSTRAINT FK_5C116121F44CABFF');
        $this->addSql('ALTER TABLE package_product DROP CONSTRAINT FK_5C1161214584665A');
        $this->addSql('DROP TABLE package_product');
        $this->addSql('ALTER TABLE product ADD package_id UUID DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN product.package_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT fk_d34a04adf44cabff FOREIGN KEY (package_id) REFERENCES package (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_d34a04adf44cabff ON product (package_id)');
    }
}
